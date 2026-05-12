<?php foreach(\App\Models\PermohonanReklame::all() as $p) { echo $p->documentRequirements->count(); }
