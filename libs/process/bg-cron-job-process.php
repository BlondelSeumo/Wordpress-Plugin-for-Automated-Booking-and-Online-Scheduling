<?php

class BG_Cron_Job_Process extends WP_Background_Process {

    /**
     * @var string
     */
    protected $action = 'automatic_update_process';

    /**
     * Task
     *
     * Override this method to perform any actions required on each
     * queue item. Return the modified item for further processing
     * in the next pass through. Or, return false to remove the
     * item from the queue.
     *
     * @param mixed $item Queue item to iterate over
     *
     * @return mixed
     */
    protected function task( $item ) {
        //error_log(current_time('mysql',0));
        $db = FAT_DB_Bookings::instance();
        $db->automatic_update_status();
        sleep( 120 );
        return false;
    }

    /**
     * Complete
     *
     * Override if applicable, but ensure that the below actions are
     * performed, or, call parent::complete().
     */
    protected function complete() {
        parent::complete();
        $url = admin_url('admin.php?process=fat_sb_cron_job');
        wp_remote_request($url);
        // Show notice to user or perform some other arbitrary task...
    }

}