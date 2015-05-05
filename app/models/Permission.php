<?php
class Permission extends Eloquent {
    //This should hold the actual permission table.. who is allowed into what.

    protected $table = 'permissions';

    //allows permission->controller
    public function controller(){
        return $this->morphTo();
    }

    //allows permission->requestor
    public function requestor(){
        return $this->morphTo();
    }



    //a permissions query you can dump into your query.
    public static function hasPermission($controller_type = '', $user){
        $out = function($query) use ($controller_type, $user){
            $query->where(function($query) use ($controller_type, $user){
                $query->where(function($query) use ($controller_type){
                    $query->where('controller_id','=','*')
                        ->orWhere('controller_type', '=', $controller_type);
                })
                ->where(function ($query) use ($user) {
                    $query->where(function ($query) use ($user) {    //where user
                        $query->where(function ($query) use ($user) {
                            $query->where('requestor_id', '=', $user->id)
                                ->orWhere('requestor_id', '=', '*');
                        })
                        ->where('requestor_type', '=', 'user');
                    })
                    ->orWhere(function ($query) use ($user) {    //where role
                        $query->where(function ($query) use ($user) {
                            $query->where('requestor_id', '=', $user->role_id)
                                ->orWhere('requestor_id', '=', '*');
                        })
                        ->where('requestor_type', '=', 'role');
                    });
                })
                ->orderBy('controller_id', 'desc')
                ->orderBy('requestor_id', 'desc')
                ->orderBy('requestor_type', 'desc');
            })
;
        };
        return array('permission' => $out);
    }

    //$perms = Auth::user()->permission()->where('controller_type','=','content')->get();
    //$c = Content::permission()->perm()->get();

    public static function checkPermission($controller_type, $controller_id = null, $message='You do not have permission'){

        $perm = self::getPermission($controller_type, $controller_id);

        if ($perm->result === false) {
            //we can redirect!
            return Redirect::guest(Utils::cmsRoute.'login')
                ->with('message', $message);
        } else {
            return(true);
        }
    }

    public static function getPermission($controller_type, $controller_id = null, $return = false){
        //check permisssion against user
        if (Auth::guest()) {
            $user = User::find(1);  //select the guest row.
        } else {
            $user = Auth::user();
        }

        //a horrible looking query that grabs the permissions for a user.
        $perm = Permission::where(function ($query) use ($controller_type, $controller_id) {
            $query->where('controller_type', '=', $controller_type)
                  ->where(function ($query) use ($controller_id) {
                        $query->where('controller_id', '=', $controller_id)
                              ->orWhere('controller_id', '=', '*');
                  });
        })
        ->where(function ($query) use ($user) {
            $query->where(function ($query) use ($user) {    //where user
                $query->where(function ($query) use ($user) {
                    $query->where('requestor_id', '=', $user->id)
                        ->orWhere('requestor_id', '=', '*');
                })
                ->where('requestor_type', '=', 'user');
            })
            ->orWhere(function ($query) use ($user) {    //where role
                $query->where(function ($query) use ($user) {
                    $query->where('requestor_id', '=', $user->role_id)
                        ->orWhere('requestor_id', '=', '*');
                })
                ->where('requestor_type', '=', 'role');
            });
        })
        ->orderBy('controller_id', 'desc')
        ->orderBy('requestor_id', 'desc')
        ->orderBy('requestor_type', 'desc')
        ->get();

        
        $return = new stdClass();
        $return->result = false;
        foreach ($perm as $p) {
            if ($p->x === '1') {
                $return->result = true;
                $return->picked = $p;
                break;
            } elseif ($p->x === "0") {
                $return->result = false;
                $return->picked = $p;
                break;
            } else {
                //var_dump($p->id);
                //we are inheriting from the enxt level up.
            }
        }

        $return->set = $perm;
        return($return);
    }

    public static function getControllerPermission($controller_id, $controllerAction){

        $perm = Permission::where(function ($query) use ($controllerAction, $controller_id) {
            $query->where('controller_type', '=', $controllerAction)
                  ->where(function ($query) use ($controller_id) {
                        $query->where('controller_id', '=', $controller_id)
                              ->orWhere('controller_id', '=', '*');
                  });
        })
        ->orderBy('controller_id', 'desc')
        ->get();
        return($perm);
    }
}