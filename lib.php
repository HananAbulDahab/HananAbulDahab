<?php
//require_login();
function local_mancookies_after_require_login() {
    global $USER;
    global $DB, $CFG;

    if (isloggedin() && !isguestuser()) {
        $cookie_name = "Moodle_iqraa_auth";
        $fromcookies="";
        if(!isset($_COOKIE[$cookie_name])) {
        } else {
            $fromcookies=$_COOKIE[$cookie_name];
        }

        $userId=$USER->id;
        
        $field = $DB->get_record('user_info_field', ['shortname' =>  'validationkey']);
        if($field ==true){
            $fieldid=$field->id;
            
            $user = $DB->get_record('user_info_data', ['userid' =>  $userId , 'fieldid' =>  $fieldid]);
            if($user == true){
                $fromDB=$user->data;
                if($fromDB==$fromcookies){
                    $authcookiesval="OK";
                }else{
                    $authcookiesval="NotOK";
                }        
                $fieldauth = $DB->get_record('user_info_field', ['shortname' =>  'authcookies']);
                if($fieldauth == true){
                    $fieldid1=$fieldauth->id;
                    $userdata = $DB->get_record('user_info_data', ['userid' =>  $userId , 'fieldid' =>  $fieldid1]);
                    if($userdata == true){
                        $userdata->data = $authcookiesval;
                        $DB->update_record('user_info_data',$userdata, $bulk=false);
                    }else{
                        $data = new \stdClass();   
                        $data->userid=$userId;
                        $data->fieldid=$fieldid;
                        $data->dataformat=0;
                        $data->data = $authcookiesval;
                        $DB->insert_record('user_info_data', $data, $bulk=false);
                    }
                }
            }
        }    
        //remove_dir($CFG->dataroot.'/cache', true);
        $USER->profile['authcookies']=$authcookiesval;
        
    }

}
