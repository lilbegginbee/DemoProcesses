var Processes = {

    status_init: 'init',
    status_wait: 'wait',
    status_progress: 'progress',
    status_done: 'done',

    children: {},

    init: function(url,$container) {
        this.url = url;
        this.$container = $container;
        this.$table = $('<table/>').append(
                        $('<thead/>').append(
                            $('<tr/>')
                                .append($('<th/>').text('ID'))
                                .append($('<th/>').addClass('col-lg-1').text('Название'))
                                .append($('<th/>').text('Время'))
                                .append($('<th/>').text('Создан'))
                                .append($('<th/>').text('Запущен'))
                                .append($('<th/>').text('Статус'))
                                .append($('<th/>').addClass('col-lg-3').text('Прогресс'))
                                .append($('<th/>').addClass('col-lg-3').text('Действия'))
                            )
                        ).append($('<tbody/>'));
        this.$table.addClass('table table-striped table-action');
        this.$container.append(this.$table);

        // very abstract timer
        setInterval(function(){ Processes._refreshChildren(); },999);
    },
    refresh: function() {
        $.getJSON (this.url + 'list',function(response) {
            this.$tbody = $('tbody', Processes.$table);
            this.$tbody.empty();

            if (!response.data.items.length) {
                Processes.$table.hide();
                return;
            }
            Processes.$table.show();

            for(index in response.data.items) {
                var process = response.data.items[index];

                $actions = $('<div/>').addClass('btn-group');
                // Действие Старт/Стоп
                if (process.can_start) {
                    $actions.append(
                        $('<button/>').attr(
                                {
                                    'rel':Processes.url + 'start',
                                    'id_process':process.id_process
                                })
                            .addClass('btn btn-success btn-xs')
                            .text('Старт')
                            .click(Processes.buttonStart)
                    );
                } else if(process.status == Processes.status_progress) {
                    $actions.append(
                        $('<button/>').attr(
                                {
                                    'rel':Processes.url + 'stop',
                                    'id_process':process.id_process
                                })
                            .addClass('btn btn-warning btn-xs')
                            .text('Стоп')
                            .click(Processes.buttonStop)
                    );
                }
                // Сброс процесса в init
                if (process.status != Processes.status_init
                     && process.status != Processes.status_done ) {
                    $actions.append(
                        $('<button/>').attr(
                            {
                                'rel':Processes.url + 'reset',
                                'id_process':process.id_process
                            }
                        )
                            .addClass('btn btn-default btn-xs')
                            .text('Сброс')
                            .click(Processes.buttonReset)
                    );
                }

                // Удаление
                $actions.append(
                    $('<button/>').attr(
                            {
                                'rel':Processes.url + 'remove',
                                'id_process':process.id_process
                            }
                        )
                        .addClass('btn btn-danger btn-xs')
                        .text('Удалить')
                        .click(Processes.buttonRemove)
                );

                //Прогресс
                var progressVal = parseInt(100 * process.progress_time / process.full_time);
                if (progressVal > 100) {
                    progressVal = 100;
                }
                $progress = null;
                if (process.status == Processes.status_progress || process.status == Processes.status_wait) {
                    $progress = $('<div class="progress progress-striped">'+
                                    '<div class="progress-bar" style="width: '+progressVal+'%">'+progressVal+'%</div>'+
                                 '</div>');
                } else if (process.status == 'done') {
                    $progress = $('<div class="progress">' +
                                    '<div class="progress-bar progress-bar-success" style="width: 100%">100%</div>' +
                                  '</div>');
                }

                if ($progress && progressVal != 100) {
                    $progress.addClass('active');
                }
                if ($progress) {
                    $progress.attr('id', 'progress-' + process.id_process);
                }

                Processes.children[process.id_process] = new processInstance(
                                                    process.id_process,
                                                    process.status,
                                                    process.full_time,
                                                    process.progress_time
                                                );
                this.$tbody.append(
                    $('<tr/>')
                        .append($('<td/>').text( process.id_process ))
                        .append($('<td/>').text( process.title ))
                        .append($('<td/>').text( process.full_time ))
                        .append($('<td/>').text( process.add_time ))
                        .append($('<td/>').text( process.start_time ))
                        .append($('<td/>').html( Processes._statusRender(process.status) ))
                        .append($('<td/>').html( $progress ))
                        .append($('<td/>').html( $actions ))
                );
            }

        })

    },
    _refreshChildren: function() {
        $.each(Processes.children, function(index,item) {
            if (item.status == Processes.status_progress) {
                item.fakeUpdate();
            }
        });
    },
    _statusRender: function(status) {
        var html = '';
        switch (status) {
            case 'done':
                html = '<span class="label label-success">'+status+'</span>';
                break;
            case 'init':
                html = '<span class="label label-default">'+status+'</span>';
                break;
            case 'wait':
                html = '<span class="label label-warning">'+status+'</span>';
                break;
            case 'progress':
                html = '<span class="label label-primary">'+status+'</span>';
                break;
            default:
                html = status;
        }
        return html;
    },

    /**
     * Набор обработчиков для кнопок-дейcтвий
     */
    buttonStart: function(e) {
        var $button = $(this);
        $button.attr('disabled','disabled');
        showMessage('Стартуем процесс...');
        $.ajaxSetup({dataType: "json"});
        $.post($(this).attr('rel'),
                {
                    'id_process':$(this).attr('id_process')
                },
                function (response) {
                    showMessage(response.data.message, true);
                    Processes.refresh();
                }
        );
    },
    buttonStop: function(e) {
        var $button = $(this);
        $button.attr('disabled','disabled');
        showMessage('Попытка остановить процесс...');
        $.ajaxSetup({dataType: "json"});
        $.post($(this).attr('rel'),
            {
                'id_process':$(this).attr('id_process')
            },
            function (response) {
                showMessage(response.data.message, true);
                Processes.refresh();

            }
        );
    },
    buttonReset: function(e) {
        var $button = $(this);
        $button.attr('disabled','disabled');
        showMessage('Попытка сброса процесса...');
        $.ajaxSetup({dataType: "json"});
        $.post($(this).attr('rel'),
            {
                'id_process':$(this).attr('id_process')
            },
            function (response) {
                showMessage(response.data.message, true);
                Processes.refresh();

            }
        );
    },
    buttonRemove: function(e) {
        var $button = $(this);
        $button.attr('disabled','disabled');
        if (!confirm("Хотите удалить процесс?")) {
            return;
        }
        showMessage('Удаляем процесс.');
        $.ajaxSetup({dataType: "json"});
        $.post($(this).attr('rel'),
            {
                'id_process':$(this).attr('id_process')
            },
            function (response) {
                $button.removeAttr('disabled');
                showMessage(response.data.message);
                Processes.refresh();
            }
        );
    }
}


function processInstance(idProcess,status,fullTime,progressTime) {

    this.idProcess = idProcess;
    this.status = status;
    this.fullTime = fullTime;
    this.progressTime = progressTime;

    this.fakeUpdate = function() {
        if(this.progressTime >= this.fullTime) {
            Processes.refresh();
            return;
        }
        this.progressTime++;
        var percentage = parseInt(100 * this.progressTime / this.fullTime);
        $('#progress-'+this.idProcess+' .progress-bar')
            .css('width', percentage + '%')
            .text(percentage + '%');
    }

    return this;
}