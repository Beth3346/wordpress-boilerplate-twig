<?php

namespace Framework\Helpers;

class Date
{

    /**
     *
     *
     * @since  1.0.0
     * @access public
     * @param
     * @return void
     */

    public function timeDiff($datetime)
    {
        $current_time = current_time('timestamp') ;

        if ($datetime) {
            $datetime = strtotime($datetime);
            echo '<span>';

            if ($datetime > $current_time) {
                echo 'in about ' . human_time_diff($datetime, current_time('timestamp'));
            } else if ($datetime = $current_time) {
                echo 'Right Now';
            } else {
                echo 'about ' . human_time_diff($datetime, current_time('timestamp')) . ' ago';
            }

            echo '</span>';
        }
    }

    /**
     *
     *
     * @since  1.0.0
     * @access public
     * @param
     * @return void
     */

    public function startEnd($start_date, $start_time, $end_date, $end_time)
    {

        if ($start_date === $end_date) {
            echo '<p>';
                echo mysql2date('l, F j, Y', $start_date);
                echo '</span>';
                echo mysql2date('g:i a', $start_time);
                echo '</span> to ';
                echo mysql2date('g:i a', $end_time);
                echo '</span>';
            echo '</p>';
        } else {
            echo '<p>';
                echo mysql2date('l, F j, Y', $start_date);
                echo '</span> at ';
                echo mysql2date('g:i a', $start_time);
                echo '</span> to ';
                echo mysql2date('l, F j, Y', $end_date);
                echo '</span> at ';
                echo mysql2date('g:i a', $end_time);
                echo '</span>';
            echo '</p>';
        }
    }

    /**
     *
     *
     * @since  1.0.0
     * @access public
     * @param
     * @return void
     */

    public function isExpired($datetime = null)
    {
        $current_time = strtotime(current_time('Y-m-d H:i'));

        if ($datetime) {
            $datetime = strtotime($datetime);
            return $datetime >= $current_time ? false : true;
        }

        return false;
    }
}