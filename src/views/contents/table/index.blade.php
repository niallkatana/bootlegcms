<?php
if(@$childrenSettings){
    $firstChildSettings = $childrenSettings->first();    
}
?>
<style>
    .setting.active{
        position: relative;
        width: 300%;
        left: -100%;
        z-index: 1;    
    }

    a.edit-field, a.ok-field{
        position: absolute;
        top:5px;
        right:5px;
        z-index: 2;
    }

    thead th{
        text-align: center;
    }

    .setting-cell{
        position: relative;
        text-align: center;
    }
    .table-actions{
        width:230px;
    }
    
</style>
    <div class='overlay'></div>
    <div class="page-header row">
        <div class='col-xs-8'>
            <h1 class="">
                <i class="glyphicon glyphicon-list-alt"></i>&nbsp;&nbsp; {{$content->name or 'Content'}}
            </h1>
        </div>
        <div class='col-xs-4'>
            <form class='table-search pull-right' action="{{action('\Bootleg\Cms\ContentsController@getSearch', array('id'=>$content->id))}}">
                <div class="input-group">
                    <input type="search" name='search' value="{{@\Input::get('search')}}" class="form-control" placeholder="search">
                    <span class='input-group-btn'>
                        <button type="submit" class="btn btn-default js-children-search"><span class='glyphicon glyphicon-search'></span></button>
                    </span>
                </div>
                <input type="hidden" name='id' value='{{$content->id}}' placeholder="search">
            </form>
        </div>
    </div>

    @include('cms::layouts.flash_messages')
    <table class="table table-striped table-bordered">
        <thead>
            <tr>
                @if(!$children[0]->hide_id)
                <th>#</th>
                @endif
                @if(!$children[0]->hide_name)
                <th>name</th>
                @endif
                @if(!$children[0]->hide_slug)
                <th>slug</th>
                @endif
                @if(@$firstChildSettings)
                    @foreach($firstChildSettings as $settingName=>$setting)
                        <th>{{$settingName}}</th>
                    @endforeach
                @endif
                <th>actions</th>
            </tr>
        </thead>
        <tbody>
            @if(@$children)
                @foreach($children as $child)
                    <tr>
                        @if(!$children[0]->hide_id && !$child->hide_id)
                        <td>{{$child->id}}</td>
                        @endif
                        @if(!$children[0]->hide_name && !$child->hide_name)
                        <th>{{$child->name}}</th>
                        @endif
                        @if(!$children[0]->hide_slug && !$child->hide_slug)
                        <td>{{$child->slug}}</td>
                        @endif
                        @foreach($childrenSettings[$child->id] as $setting)
                            <td class='setting-cell'>
                                
                                <form action='{{action('\Bootleg\Cms\ContentsController@anyUpdate', array($child->id))}}' method='POST'>
                                    <div class='setting'>
                                        <input type="hidden" name="_token" value="<?php echo csrf_token(); ?>" />
                                        @if($setting->field_type == 'upload')
                                            @if(\Bootleg\Cms\Utils::endsWith($setting->value, 'png') || \Bootleg\Cms\Utils::endsWith($setting->value, 'jpg') || \Bootleg\Cms\Utils::endsWith($setting->value, 'gif'))
                                                <div class='value {{$setting->field_type}} image'> 
                                                    <img src='{{$setting->value}}'  width='100'/>
                                                </div>
                                            @else
                                                <div class='value {{$setting->field_type}}'>    
                                                {{$setting->value}}
                                                </div>
                                            @endif
                                        @else
                                            <div class='value {{$setting->field_type}}'>    
                                                {{@$setting->value}}
                                            </div>
                                        @endif
                                    </div>     
                                </form>
                                
                                @if($setting->field_type != 'static')
                                <a href='{{action('\Bootleg\Cms\ContentsController@getRenderSetting', array($setting->id, $child->id, get_class($setting)))}}' class='js-edit-click edit-field'>
                                    <span class='glyphicon glyphicon-pencil'></span>
                                </a>
                                @endif
                            </td>
                        @endforeach
                       
                           <?php /*    @for($i=0; $i < $padCells; $i++)
                                <td class='setting-cell'>
                                    <div class='setting'>
                                    </div> 
                                <a href='{{action('\Bootleg\Cms\ContentsController@getRenderSetting', array(NULL, $setting->content_id, 'setting name'))}}' data-update-href='{{action('\Bootleg\Cms\ContentsController@anyUpdate', array($setting->content_id))}}' class='js-edit-click edit-field'><span class='glyphicon glyphicon-pencil'></span></a>
                                </td>
                            @endfor
                            */ ?>
                        <td class='table-actions'>
                            <div class="btn-group" role="group" aria-label="get children">
                                <button href='{{action('\Bootleg\Cms\ContentsController@getTable', array($child->id))}}' class='btn btn-primary btn-sm js-show-children' data-toggle="button"><span class='glyphicon glyphicon-chevron-down'></span></button>
                                <a href='{{action('\Bootleg\Cms\ContentsController@getTable', array($child->id))}}' class='btn btn-primary btn-sm '>Show Children</a>
                            </div>
                            {{--<a href='{{action('\Bootleg\Cms\ContentsController@anyEdit', array($child->id))}}' class='btn btn-warning btn-sm js-main-content'><span class='glyphicon glyphicon-pencil'></span> Edit</a> --}}
                            <a href='{{action('\Bootleg\Cms\ContentsController@anyDestroy', array($child->id))}}' class='btn btn-danger btn-sm js-delete-item'><span class='glyphicon glyphicon-remove'></span> Delete</a>
                        </td>
                    </tr>
                @endforeach
            @endif
        </tbody>
        <tfoot>
            <tr>
                <td colspan="99999">
                    @if(!$content->hide_create)
                    <div class="btn-group" role="group" aria-label="get children">
                        <button href="{{action('\Bootleg\Cms\ContentsController@anyCreate', array($content->id))}}" class="btn btn-primary btn-sm js-create-content" data-toggle="button"><span class="glyphicon glyphicon-plus"></span></button>
                        <a href="{{action('\Bootleg\Cms\ContentsController@anyCreate', array('parent_id'=>$content->id))}}" class="btn btn-primary btn-sm js-main-content">Create Content</a>
                    </div>
                    @endif
                </td>
            </tr>
        </tfoot>
    </table>
    @if(method_exists($children, 'currentPage'))
        {!!$children->appends(Input::get())->render()!!}
    @endif
    <script type="text/javascript">

        function endsWith(str, suffix) {
            console.log(str);
            console.log(suffix);
            return str.indexOf(suffix, str.length - suffix.length) !== -1;
        }

        $(function () {
            $('[data-toggle="popover"]').popover()
            if(typeof(jsDeleteClick) === 'undefined'){
                jsDeleteClick = true;
                $('.main-content').on('click', '.js-delete-item', function(e){
                    $me=$(this);
                    e.preventDefault();
                    swal({
                        title: "Are you sure?",
                        type: "warning",
                        text: "Are you sure you want to delete?",
                        showCancelButton: true,
                        confirmButtonText: "Yes, delete it!"
                    },
                    function(){   
                        $.get($me.attr('href'), function(data){
                            if($me.closest('tr').hasClass('expanded')){
                                $me.closest('tr').next('tr.children').remove();
                            }
                            $me.closest('tr').remove();
                            
                        });
                    });
                });
            }
            
            if(typeof(jsEditClick) === 'undefined'){
                jsEditClick = true;
                //Edit pencil button
                $('.main-content').on('click', '.js-edit-click', function(e){
                    e.preventDefault();
                    $me = $(this);
                    $td = $me.closest('td');

                    $.get($me.attr('href'), function(data){
                        var $data = $.parseHTML(data);
                        data+= '<button class="btn btn-small btn-success js-submit-setting">OK</button>';
                        $('form .setting', $td).popover({html:true, content:data}).popover('toggle');
                    });    
                });

                //OK on popover box
                $('.main-content').on('click', '.js-submit-setting', function(e){
                    e.preventDefault();
                    $me = $(this);
                    $form = $me.closest('form');
                    $td = $me.closest('td');
                    $.post($form.attr('action'), $form.serialize(), function(data){
                        $('form .setting', $td).popover('hide');
                        var formValue = $('.form-control', $form).val();
                        if(endsWith(formValue,'png') || endsWith(formValue,'jpg') || endsWith(formValue,'gif')){
                            formValue = '<img width="100" src="'+formValue+'" />';
                        }
                        $('form .setting .value', $td).html(formValue);
                    });
                });
            }

            

            if(typeof(jsShowChildrenReg) === 'undefined'){
                jsShowChildrenReg = true;
                $('.main-content').on('click', '.js-show-children', function(e){
                    e.preventDefault();
                    e.stopPropagation();
                    var $parentRow = $(this).closest('tr');
                    if($parentRow.hasClass('expanded')){
                        $parentRow.next('tr.children').remove();
                    }
                    else{
                        $.get($(this).attr('href'), function(data){
                            //alert('arse');
                            $childrenRow = $('<tr class="children"><td colspan="999999"><div class="child">'+data+'</div></td></tr>');
                            $parentRow.after($childrenRow);
                           // $('tr', $childrenRow).removeClass('nice-hidden');
                        });
                    }
                    $parentRow.toggleClass('expanded');
                    
                });
            }

            if(typeof(jsChildrenSearch) === 'undefined'){
                jsChildrenSearch = true;
                $('.main-content').on('click', '.js-children-search', function(e){
                    e.preventDefault();
                    e.stopPropagation();
                    $form = $(this).closest('form');
                    alert($form.attr('action'));
                    $.get($form.attr('action'), $form.serialize(), function(data){
                        $('.main-content').html(data);
                    });
                });
            }
            
        });
    </script>