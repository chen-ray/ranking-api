<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>世界羽联排名</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet" crossorigin="anonymous">
        <script src="https://code.jquery.com/jquery-3.7.1.min.js"  crossorigin="anonymous"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.js"  crossorigin="anonymous"></script>
        <script type="text/javascript">
            function multiCollapseConsole(number) {
                console.log('number=>', number);
                const arr = [1, 2, 3, 4, 5];
                //arr.forEach(function(i) {
                    //let collapse = new bootstrap.Collapse('#multiCollapse' + i);
                    //collapse.hide();
                    //let button = $('#collapseButton' + i);
                    //console.log(button.classList);
                    //button.removeClass('btn-primary').addClass('btn-secondary');
                //});

                $('.collapse').hide();
                $('.collapseBut').removeClass('btn-primary').addClass('btn-secondary');
                $('#multiCollapse' + number).slideDown();
                const operationButton = $('#collapseButton' + number);
                operationButton.removeClass('btn-secondary').addClass('btn-primary');
                console.log(operationButton);
            }
            $(function() {
                multiCollapseConsole(1);
            });
        </script>
    </head>
    <body class="p-4">

    <div class="container text-center border border-success rounded p-4">
        <div class="row">
            <div class="col">
                <h1 class="text-3xl font-bold underline text-center p-3">
                    世界羽联排名第[{{ $publicationId }}]期
                </h1>
            </div>
        </div>

        <div class="row p-2">
            <div class="col">
            <button class="btn btn-secondary collapseBut" id="collapseButton1" type="button" aria-expanded="false" onclick="multiCollapseConsole(1)">男单</button>
            <button class="btn btn-secondary collapseBut" id="collapseButton2" type="button" aria-expanded="false" onclick="multiCollapseConsole(2)">女单</button>
            <button class="btn btn-secondary collapseBut" id="collapseButton3" type="button" aria-expanded="false" onclick="multiCollapseConsole(3)">男双</button>
            <button class="btn btn-secondary collapseBut" id="collapseButton4" type="button" aria-expanded="false" onclick="multiCollapseConsole(4)">女双</button>
            <button class="btn btn-secondary collapseBut" id="collapseButton5" type="button" aria-expanded="false" onclick="multiCollapseConsole(5)">混双</button>
            </div>
        </div>

        <div class="row collapse multi-collapse collapse-horizontal" id="multiCollapse1">
            @include('shared/table', ['title' => '男单', 'rankings' => $men])
        </div>
        <div class="row collapse multi-collapse collapse-horizontal" id="multiCollapse2">
            @include('shared/table', ['title' => '女单', 'rankings' => $women])
        </div>
        <div class="row collapse multi-collapse collapse-horizontal" id="multiCollapse3">
            @include('shared/table', ['title' => '男双', 'rankings' => $manDoubles])
        </div>
        <div class="row collapse multi-collapse collapse-horizontal" id="multiCollapse4">
            @include('shared/table', ['title' => '女双', 'rankings' => $womanDoubles])
        </div>
        <div class="row collapse multi-collapse" id="multiCollapse5">
            @include('shared/table', ['title' => '混双', 'rankings' => $mixedDoubles])
        </div>

        <script type="text/javascript">
            function showBreakdownModal(rankingId){
                const myModal = new bootstrap.Modal('#breakdownModal', {});
                myModal.show();

                let url = '{{ route('apiBreakdowns', ['rankingId' => 9999]) }}';
                url = url.replace('9999', rankingId);
                console.log('rankingId', rankingId);
                console.log('url', url);

                $.getJSON(url, function( json ) {
                    let newHtml = '';
                    $.each( json.data, function( key, val ) {
                        console.log('val', val.date, val.name);
                        newHtml += "<tr><td>" + val.date + "</td><td class='text-start'>" + val.name + "</td><td>"+val.result+"</td>";
                        newHtml += "<td>" + val.points + "</td>"
                        newHtml += "<td><a href='"+ val.url +"'>官网</a></td></tr>";
                    });
                    console.log('newHtml', newHtml);
                    $('#modalBody').html(newHtml);
                });
            }
            $(document).ready(function (){
                console.log('document is ready');
            })
        </script>
        <!-- Modal -->
        <div class="modal fade" id="breakdownModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-xl">
                <div class="modal-content">
                    <div class="modal-header">
                        <h1 class="modal-title fs-5" id="exampleModalLabel">征战记录</h1>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <table class="table table-striped table-hover table-bordered">
                            <thead>
                            <tr>
                                <th scope="col">年份/星期</th>
                                <th scope="col">比赛名称</th>
                                <th scope="col">名次</th>
                                <th scope="col">POINTS</th>
                                <th scope="col">操作</th>
                            </tr>
                            </thead>
                            <tbody class="table-group-divider" id="modalBody">
                            </tbody>
                        </table>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    </body>
</html>
