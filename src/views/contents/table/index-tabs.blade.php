<?php
    if(count($settings) == 0){
        $settings['Content'] = true;
    }
    $i = 0; //count for the tab.
?>
@if($content_mode == 'template')
{!! Form::model($content, array('method' => 'POST', 'files'=>true, 'data-lang-code'=>\App::getLocale(), 'class'=>'main-form tab-content col-sm-12 tab-language tab-language-'.\App::getLocale(), 'action' => array('\Bootleg\Cms\TemplateController@anyUpdate', @$content->id))) !!}
@else
{!! Form::model($content, array('method' => 'POST', 'files'=>true, 'data-lang-code'=>\App::getLocale(),  'class'=>'main-form tab-content col-sm-12 tab-language tab-language-'.\App::getLocale(), 'action' => array('\Bootleg\Cms\ContentsController@anyUpdate', @$content->id))) !!}
@endif
    <h4>{{App::getLocale()}} 
        @if(\App::getLocale() != $application->default_locale)
            <button type="button" class="close js-close-language-tab" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        @endif
    </h4>
    @foreach($settings as $key=>$section)
        <?php
        $fields = "";
        if($section !== true){
            $model = new Baum\Extensions\Eloquent\Collection;
            foreach($section as $flds){
                $model->push($flds);
            }
            $fields = $model->groupBy('name');  
        }
        ?>
        <div class="tab-pane {{$i==0?'active in':''}} fade edit-{{$key}}-tab" id="tab-{{$key}}">
            <ul>
                @if($key == 'Content')
                    <li class="form-group">
                        {!! Form::label('name', 'Name:') !!}
                        {!! Form::text('name', null, array('class'=>'form-control js-content-name')) !!}
                    </li>
                    <li class="form-group">
                        <label>Status:</label>
                        <div class="radio">
                            <label>
                                {!! Form::radio('status','0','') !!}
                                Draft
                            </label>
                        </div>
                        <div class="radio">
                            <label>
                                {!!Form::radio('status','1','') !!}
                                Published
                            </label>
                        </div>
                    </li>
                @endif
                @if(@$fields)
                    @foreach($fields as $field)
                    {{-- This is where the custom input types are rendered in. --}}
                        <li class="form-group">
                            <?php
                            $view = @$field[0]->field_type?$field[0]->field_type:'text';
                            ?>
                            @include("cms::contents.input_types.$view", array('setting'=>$field, 'contentItem'=>$content))
                        </li>
                    @endforeach
                @endif

                <li class="form-group">
                    <div class='btn-group btn-group-lg'>
                        {!! Form::submit(@$content->id?trans('cms::messages.button.update'):trans('cms::messages.button.create'), array('class' => 'btn btn-success js-content-update', 'data-loading-text'=>'Loading...')) !!}
                        {!! link_to_action('\Bootleg\Cms\ContentsController@anyEdit', trans('cms::messages.button.cancel'), @$content->id, array('class' => 'btn btn-danger js-content-cancel')) !!}
                    </div>
                </li>
            </ul>
        </div>
        <?php $i++ //increment the tab number?>
    @endforeach
        <div class="tab-pane fade edit-advanced-tab" id="tab-advanced">
            <ul>
                <li class="form-group">
                    {!! Form::label('slug', 'Slug:') !!} <button class='btn btn-default btn-xs js-generate-slug'>generate</button>
                    <div class="input-group">

                        <?php
                        $niceFullSlug = "http://".ApplicationUrl::getApplicationUrl()->domain;
                        $niceFullSlug .= ApplicationUrl::getApplicationUrl()->folder=='/'?'':ApplicationUrl::getApplicationUrl()->folder;
                        ?>
                        <span class="input-group-addon">{{$niceFullSlug}}</span>
                    {!! Form::text('slug', null, array('class'=>'form-control js-slug')) !!}
                    </div>
                </li>

                <li class="form-group">
                    {!! Form::label('identifier', 'Identifier:') !!}
                    {!! Form::input('identifier', 'identifier', null, array('class'=>'form-control')) !!}
                </li>
                
                <li class="form-group">
                    {!! Form::label('package', 'Package:') !!}
                    {!! Form::input('text', 'package', null, array('class'=>'form-control')) !!}
                </li>
                <li class="form-group">
                    {!! Form::label('view', 'View:') !!}
                    {!! Form::input('text', 'view', null, array('class'=>'form-control')) !!}
                </li>
                <li class="form-group">
                    {!! Form::label('headers', 'Headers:') !!}
                    {!! Form::input('text', 'headers', null, array('class'=>'form-control')) !!}
                </li>
                @if($content_mode == 'contents')
                    <li class="form-group">
                        {!! Form::label('template_id', 'Template ID:') !!}
                        {!! Form::input('number', 'template_id', null, array('class'=>'form-control')) !!}
                    </li>
                @else
                    <li class="form-group">
                        <div class="checkbox">
                            <label>
                                {!! Form::checkbox('loopback','1',$content->loopback) !!}
                                Loopback
                            </label>
                        </div>
                        <div class="checkbox">
                            <label>
                                {!! Form::checkbox('auto_create','1',$content->auto_create) !!}
                                Auto Create
                            </label>
                        </div>
                    </li>
                @endif
            </ul>
        </div>

        <div class="tab-pane fade edit-permission-tab" id="tab-permission">
            @include('cms::contents.permission', array('content'=>@$content, 'permission'=>@$permission))
        </div>

{!! Form::close() !!}
