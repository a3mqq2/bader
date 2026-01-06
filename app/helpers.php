<?php


   function get_area_name() {
       $current_url = request()->url();
         $area = (explode('/', $current_url)[3]);
         $area = str_replace('-', '_', $area);
         return $area;
   }

   /**
    * تحويل التاريخ إلى تنسيق عربي كامل
    * @param mixed $date التاريخ (Carbon, string, or null)
    * @param string $format التنسيق المطلوب (full, date, datetime, time)
    * @return string التاريخ بالعربية
    */
   function arabicDate($date, string $format = 'date'): string
   {
       if (!$date) {
           return '-';
       }

       if (!$date instanceof \Carbon\Carbon) {
           $date = \Carbon\Carbon::parse($date);
       }

       $date->locale('ar');

       return match($format) {
           'full' => $date->translatedFormat('l j F Y'),           // الإثنين 6 يناير 2026
           'date' => $date->translatedFormat('j F Y'),             // 6 يناير 2026
           'datetime' => $date->translatedFormat('j F Y - h:i A'), // 6 يناير 2026 - 02:30 م
           'time' => $date->translatedFormat('h:i A'),             // 02:30 م
           'short' => $date->translatedFormat('j M Y'),            // 6 ينا 2026
           'day' => $date->translatedFormat('l'),                  // الإثنين
           'month_year' => $date->translatedFormat('F Y'),         // يناير 2026
           default => $date->translatedFormat('j F Y'),
       };
   }