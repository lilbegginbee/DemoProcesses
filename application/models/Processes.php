<?php

/**
 * Модель данных для абстрактных процессов.
 * Процессы можно:
 *  добавлять
 *  удалять
 *  стартовать
 *  останавливать
 *  проверять на завершённость
 */
class Model_Processes extends Model_Core
{
    protected $_name = 'processes';

    /**
     * Все статусы жизненного цикла процесса.
     */
    const STATUS_INIT = 'init';
    const STATUS_WAIT = 'wait';
    const STATUS_PROGRESS = 'progress';
    const STATUS_DONE = 'done';

    /**
     * @param int $idProcess
     * @return object
     */
    public function get ($idProcess)
    {
        return $this->select()
                    ->where('id_process = ?', $idProcess)
                    ->query()
                    ->fetchObject();
    }

    /**
     * @param int $idOwner
     * @return array
     */
    public function getAll ($idOwner)
    {
        $select = $this->select()
            ->where('is_removed = 0')
            ->order('add_time DESC');
        if (!is_null($idOwner)) {
            $select->where('id_owner = ?',$idOwner);
        }

        $items = $select->query()->fetchAll();
        /**
         * Нужные вычисляемые поля + рефреш процесса
         * can_start - можно ли стартовать процесс
         */
        foreach ($items as $key => $item) {
            $items[$key] = $this->updateProcess($item);
            $items[$key]['can_start'] = ($item['status'] != self::STATUS_PROGRESS && $item['status'] != self::STATUS_DONE)?1:0;
            $items[$key]['add_time'] = date('H:i:s d/m/Y', strtotime($items[$key]['add_time'] ));
            if (!is_null($items[$key]['start_time'])) {
                $items[$key]['start_time'] = date('H:i:s d/m/Y', strtotime($items[$key]['start_time'] ));
            }
            else {
                $items[$key]['start_time'] = '';
            }
        }
        return $items;
    }

    /**
     * Принудительно обновить процесс:
     *  прогресс
     *  статус
     * @param array $process
     * @return array
     */
    protected function updateProcess ($process)
    {
        switch ($process['status']) {
            case self::STATUS_PROGRESS:
                $progressUpdateTime = strtotime('now');
                $progressAdd = $progressUpdateTime - $process['progress_update_time'];
                $progressTime = $process['progress_time'] + $progressAdd;

                if ($progressTime >= $process['full_time']) {
                    $process['status'] = self::STATUS_DONE;
                }

                $this->update(
                    array(
                        'progress_time' => $progressTime,
                        'progress_update_time' => $progressUpdateTime,
                        'status' => $process['status']
                    ),
                    array(
                        'id_process = ?' => $process['id_process']
                    )
                );

                $process['progress_time'] = $progressTime;
                $process['progress_update_time'] = $progressUpdateTime;
                break;
        }

        return $process;
    }

    /**
     * Добавляет новый процесс.
     *
     * @param int $idUser
     * @param int $title
     * @return mixed
     */
    public function add ($idUser, $title, $fullTime)
    {
        return $this->insert(
            array(
                'title' => $title,
                'id_owner' => $idUser,
                'full_time' => $fullTime,
                'add_time' => date('Y-m-d H:i:s')
            )
        );
    }

    /**
     * Удаление процесса.
     * Процесс помечается, как удалённый.
     * Процесс могут удалить его владелец и админ.
     *
     * @param int $idProcess
     * @param int $idUser
     * @return bool
     */
    public function remove ($idProcess, $idUser)
    {
        if (!$this->isProcessOwnerAndAdmin($idProcess, $idUser)) {
            throw new Exception('У вас нет прав удалить процесс');
        }

        return $this->update(
            array('is_removed' => 1),
            array('id_process = ?' => $idProcess)
        );
    }

    /**
     * Стартует процесс.
     * Запускать процесс может его владелец и админ.
     *
     * @param int $idProcess
     * @param int $idUser
     * @return bool
     */
    public function start ($idProcess, $idUser)
    {
        if (!$this->isProcessOwnerAndAdmin($idProcess, $idUser)) {
            throw new Exception('У вас нет прав запустить процесс');
        }

        $Process = $this->get($idProcess);
        if (!$Process) {
            return false;
        }

        // А если процесс уже выполняется или уже выполнился когда-то,
        // то ничего не делается.
        if ($Process->status == self::STATUS_PROGRESS
             || $Process->status == self::STATUS_DONE
             || $Process->is_removed) {
            return true;
        }

        $changeTime = $startTime = date('Y-m-d H:i:s');
        // А если процесс стартует не первый раз, надо не потерять изначальное время старта
        if ($Process->status != self::STATUS_INIT) {
            $startTime = $Process->start_time;
        }

        return $this->update(
            array(
                'status' => self::STATUS_PROGRESS,
                'progress_update_time' => strtotime('now'),
                'change_time' => $changeTime,
                'start_time' => $startTime
            ),
            array(
                'id_process = ?' => $idProcess
            )
        );
    }

    /**
     * Останавливает процесс.
     * Останавливать может владелец процесса и админ.
     *
     * @param int $idProcess
     * @param int $idUser
     * @return bool
     */
    public function stop ($idProcess, $idUser)
    {
        if (!$this->isProcessOwnerAndAdmin($idProcess, $idUser)) {
            throw new Exception('У вас нет прав остановить процесс');
            return false;
        }

        $Process = $this->get($idProcess);
        if (!$Process) {
            return false;
        }

        // А если процесс не в статусе прогресс или вообще удалён,
        // то ничего не делается.
        if ($Process->status != self::STATUS_PROGRESS
            || $Process->is_removed) {
            return true;
        }

        $changeTime = date('Y-m-d H:i:s');
        $progressTime = strtotime($changeTime) - strtotime($Process->change_time);
        $progressTime += $Process->progress_time;

        // Время выполения процесса может по каким-то причинам быть больше своего изначального времени выполнения
        if ($progressTime >= $Process->full_time) {
            $status = self::STATUS_DONE;
        } else {
            $status = self::STATUS_WAIT;
        }

        return $this->update(
            array(
                'status' => $status,
                'change_time' => $changeTime,
                'progress_time' => $progressTime
            ),
            array(
                'id_process = ?' => $idProcess
            )
        );
    }

    /**
     * Сбрасывает прогресс выполнения процесса в исходное положение.
     *
     * @param int $idProcess
     * @param int $idUser
     * @return bool
     */
    public function reset ($idProcess, $idUser)
    {
        if (!$this->isProcessOwnerAndAdmin($idProcess, $idUser)) {
            throw new Exception('У вас нет прав сбросить процесс');
            return false;
        }

        $Process = $this->get($idProcess);
        if (!$Process) {
            return false;
        }

        // А если процесс не в статусе прогресс или вообще удалён,
        // то ничего не делается.
        if ($Process->status == self::STATUS_DONE
            || $Process->status == self::STATUS_INIT
            || $Process->is_removed) {
            return true;
        }

        return $this->update(
            array(
                'status' => self::STATUS_INIT,
                'change_time' => date('Y-m-d H:i:s'),
                'progress_time' => 0
            ),
            array(
                'id_process = ?' => $idProcess
            )
        );
    }

    /**
     * Проверяет, является ли данный пользователь владельцем процесса.
     *
     * @param int $idProcess
     * @param int $idUser
     * @return bool
     */
    public function isProcessOwner ($idProcess, $idUser)
    {
        $Process = $this->select()
            ->where('id_process = ?', $idProcess)
            ->query()
            ->fetchObject();
        if ($Process && $Process->id_owner == $idUser) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Проверяет, является ли данный пользователь владельцем процесса или админом
     *
     * @param int $idProcess
     * @param int $idUser
     * @return bool
     */
    public function isProcessOwnerAndAdmin ($idProcess, $idUser)
    {
        if (!$this->isProcessOwner ($idProcess, $idUser)
            && CORE_User::getGroup() != CORE_User::GROUP_ADMIN) {
            return false;
        } else {
            return true;
        }
    }
}