<?php

class AuditTrail extends \Eloquent {

	protected $table = 'audittrail';
    protected $fillable = ['id', 'userid', 'transactiontype', 'module', 'usecase', 'transactiondetails', 'transactiondate', 'status', 'browserdetails', 'browser', 'version', 'useragent', 'os', 'ismobile', 'ipaddress', 'url', 'isupdate', 'prejson', 'postjson', 'jsondiff', 'companyid', 'actionid', 'entityid'];
    public $timestamps = false;
    
    public function __construct() {
        parent::__construct();

        $this->ismobile = 0;
    }

    function user(){
        return $this->hasOne('App\User', 'id', 'userid');
    }
    function getUser(){
        if(!$this->user){
            return new User;
        }
        return $this->user;
    }

    
    static function notify($audit_values = array()){
      if(count($audit_values) > 0){
        $audit = new AuditTrail;
        $audit->fill($audit_values);
        $audit->save();
      }
      return true;
    }

}