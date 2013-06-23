<?php /* @var $this Menta_PHPUnit_Listener_Resources_HtmlResultView */ ?>
<html>
<head>
    <title>PHPUnit HTML test report</title>
    <style type="text/css">
        body {
            font-family: arial, verdana, sans-serif;
            font-size: 12px;
        }

        div.suite,
        div.browser {
            border-left: 6px solid #ccc;
            border-top: 1px solid #ccc;
            margin: 5px 0 0 10px;
            padding: 3px 0 0 3px;
            overflow: hidden;
            color: #ccc;
        }

        div.suite:hover,
        div.browser:hover {
            color: black;
            border-color: #777;
        }

        div.wrapper {
            border: none;
            padding: 0;
            margin: 0;
        }

        div.browser {
            float: left;
        }

        h2 {
            font-size: 12px;
            margin: 0;
        }

        div.test {
            border-style: solid;
            border-width: 1px 0 0 6px;
            position: relative;
            margin: 4px 0 0 10px;
            padding: 3px 30px 3px 10px;
        }

        div.test:hover {
            border-color: #777;
        }

        .test {
            min-width: 300px;
            overflow: hidden;
        }

        .dataset .test {
            min-width: 280px;
        }

        .error {
            border-color: #C20000;
            background-color: #FFFBD3;
            color: #C20000;
        }

        .failed {
            border-color: #C20000;
            background-color: #FFFBD3;
            color: #C20000;
        }

        .passed {
            border-color: #65C400;
            background-color: #DBFFB4;
            color: #3D7700;
        }

        .skipped {
            border-color: aqua;
            background-color: #E0FFFF;
            color: #001111;
        }

        .incomplete {
            border-color: #FAF834;
            background-color: #FCFB98;
            color: #131313;
        }

        .duration {
            position: absolute;
            top: 2px;
            right: 2px;
            font-size: 9px;
        }

        pre {
            margin: 0;
            padding: 0;
            overflow: auto;
        }

        ul {
            padding: 0;
            margin: 0;
        }

        li {
            list-style: none;
            padding: 0 5px;
            margin: 0 0 0 5px;
        }

        .legend li {
            border-top: 1px solid;
            border-left: 5px solid;
            float: left;
        }

        li.label {
            border: none;
            font-weight: bold;
        }

        .legend,
        .bar {
            overflow: hidden;
            margin-bottom: 10px;
        }

        #progress-wrapper {
            width: auto;
            height: 30px;
        }

        .progress-value {
            height: 30px;
            display: block;
            float: left;
        }

        .progress-inner {
            border-style: solid;
            border-width: 1px 0 0 5px;
            display: block;
            height: 29px;
            padding: 3px;
        }

        .toggle {
            text-decoration: none;
            color: black;
            background-color: rgba(255, 255, 255, 0.8);
            padding: 2px 5px;
            margin-left: 3px;
        }

        .description {
            font-style: italic;
            background-color: rgba(255, 255, 255, 0.8);
            padding: 3px;
        }
    </style>
    <script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.6/jquery.min.js"></script>
    <script type="text/javascript">
        $(function () {

            function updateVisibility() {
                $('.legend input').each(function () {
                    var $this = $(this);
                    var type = $this.attr('id').split('_')[1];
                    $('div.test.' + type).toggle($this.is(':checked'));
                });
                $('div.suite, div.browser').show().each(function () {
                    var $this = $(this);
                    if ($this.find('.test:visible').length == 0) {
                        $this.hide();
                    }
                });
                var duration = getSelectedDuration();
                if (duration > 180) {
                    duration = Math.floor(duration / 60) + ' min';
                } else {
                    duration += ' sec';
                }
                $('#selected-duration').text(duration);
            }

            function getSelectedDuration() {
                var duration = 0;
                $('.suites .test:visible').each(function () {
                    duration += parseFloat($(this).find('.duration').text().replace(/s/g, ''));
                });
                return Math.round(duration);
            }

            function updateHeatmap() {
                if ($('#feature_duration').is(':checked')) {
                    var max = 0;
                    $('.suites .test').each(function () {
                        var $this = $(this);
                        var duration = parseFloat($this.find('.duration').text().replace(/s/g, ''));
                        $this.data('duration', duration);
                        if (duration > max) {
                            max = duration
                        }
                    }).each(function () {
                            var $this = $(this);
                            var percent = ($this.data('duration') / max) * 100;
                            $this.css('background-color', getColor(percent));
                        });
                } else {
                    $('.suites .test').each(function () {
                        $(this).css('background-color', '');
                    });
                }
            }

            function getColor(percent) {
                var a = { r: 255, g: 0, b: 0 } // start color
                var b = { r: 255, g: 255, b: 0 } // end color
                var c = { // color representing percentage value
                    r: parseInt((b.r + ((percent * (a.r - b.r)) / 100)).toFixed(0)),
                    g: parseInt((b.g + ((percent * (a.g - b.g)) / 100)).toFixed(0)),
                    b: parseInt((b.b + ((percent * (a.b - b.b)) / 100)).toFixed(0))
                }
                return 'rgb(' + c.r + ',' + c.g + ',' + c.b + ')';
            }

            $('.test').each(function () {
                $this = $(this);
                $header = $this.find('h2');
                if ($this.find('.exception').length) {
                    $header.append('<a title="Toggle exception" href="#" class="toggle toggle-exception">E</a>');
                }
                if ($this.find('.screenshots').length) {
                    $header.append('<a title="Toggle screenshots" href="#" class="toggle toggle-screenshots">S</a>');
                }
                if ($this.find('.description').length) {
                    $header.append('<a title="Toggle description" href="#" class="toggle toggle-description">D</a>');
                }
            });
            $('.exception, .screenshots').hide();
            $('.exception, .description').hide();

            $('.test .toggle-exception').click(function () {
                $(this).parents('.test').find('.exception').toggle();
                return false;
            });
            $('.test .toggle-screenshots').click(function () {
                $(this).parents('.test').find('.screenshots').toggle();
                return false;
            });
            $('.test .toggle-description').click(function () {
                $(this).parents('.test').find('.description').toggle();
                return false;
            });

            $('#show-all-screenshots').click(function () {
                $('.screenshots').show();
            })
            $('#hide-all-screenshots').click(function () {
                $('.screenshots').hide();
            })
            $('#show-all-exceptions').click(function () {
                $('.exception').show();
            })
            $('#hide-all-exceptions').click(function () {
                $('.exception').hide();
            })
            $('#show-all-descriptions').click(function () {
                $('.description').show();
            })
            $('#hide-all-descriptions').click(function () {
                $('.description').hide();
            })


            updateVisibility();
            $('.filter input').click(updateVisibility);

            updateHeatmap();
            $('#feature_duration').click(updateHeatmap);
        });
    </script>
</head>
<body>

<div class="bar">
    <div id="progress-wrapper">
        <?php foreach ($this->get('percentages') as $status => $percent): ?>
            <?php $speakingStatus = $this->getStatusName($status); ?>
            <div class="progress-value" style="width: <?php echo $percent; ?>%">
                <div class="progress-inner <?php echo $speakingStatus ?>">
                    <?php echo ucfirst($speakingStatus); ?>: <?php echo round($percent); ?>%
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<div class="legend">
    <ul>
        <li class="label">Filter:</li>
        <?php foreach ($this->get('count') as $status => $count): ?>
            <?php $speakingStatus = $this->getStatusName($status); ?>
            <li class="filter <?php echo $speakingStatus ?>">
                <input type="checkbox" checked="checked" id="type_<?php echo $speakingStatus ?>"><label
                    for="type_<?php echo $speakingStatus ?>"><?php echo ucfirst($speakingStatus); ?>
                    (<?php echo $count; ?>)</label>
            </li>
        <?php endforeach; ?>
        <li class="label">Features:</li>
        <li><input type="checkbox" id="feature_duration"><label for="feature_duration">Duration Heatmap</label></li>

        <li class="label">Duration:</li>
        <li id="selected-duration"></li>

        <li class="label">Screenshots:</li>
        <li><a href="#" id="show-all-screenshots">Show</a> | <a href="#" id="hide-all-screenshots">Hide</a></li>

        <li class="label">Exceptions:</li>
        <li><a href="#" id="show-all-exceptions">Show</a> | <a href="#" id="hide-all-exceptions">Hide</a></li>

        <li class="label">Descriptions:</li>
        <li><a href="#" id="show-all-descriptions">Show</a> | <a href="#" id="hide-all-descriptions">Hide</a></li>
    </ul>
</div>

<?php echo $this->printResult($this->get('results')) ?>

</body>
</html>
