<?php 


   function get_area_name() {
       $current_url = request()->url();
         $area = (explode('/', $current_url)[3]);
         $area = str_replace('-', '_', $area);
         return $area;
   }