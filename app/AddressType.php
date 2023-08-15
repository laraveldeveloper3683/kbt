<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Wildside\Userstamps\Userstamps;

class AddressType extends Model
{
  use Userstamps;

  protected $table = 'kbt_address_type';
  protected $primaryKey = 'pk_address_type';

  public function addressType(){
    return $this->hasOne(CustomerAddres::class, 'pk_address_type', 'pk_address_type');
  }

}
