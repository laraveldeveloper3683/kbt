<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Wildside\Userstamps\Userstamps;

class CustomerFamilyRelation extends Model
{
  protected $table = 'kbt_customer_family_relation';
  protected $primaryKey = 'pk_customer_family_relation';
  use Userstamps;
}
