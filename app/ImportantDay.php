<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Wildside\Userstamps\Userstamps;

class ImportantDay extends Model
{
  protected $table = 'kbt_important_day';
  protected $primaryKey = 'pk_important_day';
  use Userstamps;
}
