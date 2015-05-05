<?php
class Application extends Eloquent {
    protected $table = 'applications';
    protected $fillable = array('name', 'theme_id', 'parent_id', 'cms_theme_id', 'cms_package', 'cms_service_provider', 'package', 'service_provider');
    protected $_settings = NULL; //holds settings for this application item so we don't have to contantly query it.
    
    public static $rules = array(
		//'content' => 'required',
		//'parent_id' => 'required'
    );
    
    public function url(){
        return($this->hasMany('ApplicationUrl'));
    }
    
    public function setting(){
        return($this->hasMany('Applicationsetting'));
    }
    
    public function languages(){
        return($this->hasMany('ApplicationLanguage'));
    }

    public function plugins(){
        return($this->belongsToMany('Plugin'));
    }
    
    public function permission(){
        return $this->morphMany('Permission', 'controller');
    }
    
    public static function getApplication($domain='', $folder = ''){        
        return(unserialize($GLOBALS['application']));

    }
    

        /*
     * returns a single setting given the name;
     */
    public function getSetting($getSetting){
        $settings = $this->setting->filter(function($model) use(&$getSetting){
            return $model->name === $getSetting;
            
        });
        if($settings->count() == 0){
            return null;
        }
        if($settings->count() > 1){
            $return = array();
            foreach($settings as $setting){
                $return[] = $setting->value;
            }
        }
        else{
            $return = $settings->first()->value;
        }
        return($return);
    }
}