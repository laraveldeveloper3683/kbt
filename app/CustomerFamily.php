<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Wildside\Userstamps\Userstamps;

class CustomerFamily extends Model
{
  protected $table = 'kbt_customer_family';
  protected $primaryKey = 'pk_customer_family';
  use Userstamps;

  public function family(){
    return $this->hasOne(ImportantDay::class, 'pk_important_day', 'pk_important_day');
  }

}
