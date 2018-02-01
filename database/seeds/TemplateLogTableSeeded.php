<?php

use Illuminate\Database\Seeder;

class TemplateLogTableSeeded extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('template_logs')->truncate();

        DB::table('template_logs')->insert([
            [
                'action_id' => \Config::get('constants.FUNCTIONALITY_TYPE.TASK.ADD'),
                'type'=>'1',
                'tags'=>'#user_first_name#, #task_name#',
                'message'=>'#user_first_name# has created task #task_name#',
                'created_at' => date('Y-m-d h:i:s'),
            ],
            [
                'action_id' => \Config::get('constants.FUNCTIONALITY_TYPE.TASK.EDIT'),
                'type'=>'1',
                'tags'=>'#user_first_name#, #task_name#',
                'message'=>'#user_first_name# has edit task #task_name#',
                'created_at' => date('Y-m-d h:i:s'),
            ],
            [
                'action_id' => \Config::get('constants.FUNCTIONALITY_TYPE.TASK.DELETE'),
                'type'=>'1',
                'tags'=>'#user_first_name#, #task_name#',
                'message'=>'#user_first_name# has delete task #task_name#',
                'created_at' => date('Y-m-d h:i:s'),
            ],
            [
                'action_id' => \Config::get('constants.FUNCTIONALITY_TYPE.TASK.START-TASK'),
                'type'=>'1',
                'tags'=>'#user_first_name#, #task_name#',
                'message'=>'#user_first_name# has start task from #task_name#',
                'created_at' => date('Y-m-d h:i:s'),
            ],
            [
                'action_id' => \Config::get('constants.FUNCTIONALITY_TYPE.TASK.PRIORITY'),
                'type'=>'1',
                'tags'=>'#user_first_name#, #task_name#',
                'message'=>'#user_first_name# has priority task #task_name#',
                'created_at' => date('Y-m-d h:i:s'),
            ],
            [
                'action_id' => \Config::get('constants.FUNCTIONALITY_TYPE.TASK.COMMENT-TASK'),
                'type'=>'1',
                'tags'=>'#user_first_name#, #task_name#',
                'message'=>'#user_first_name# had comment on task #task_name#',
                'created_at' => date('Y-m-d h:i:s'),
            ],
            [
                'action_id' => \Config::get('constants.FUNCTIONALITY_TYPE.TASK.STOP-TASK'),
                'type'=>'1',
                'tags'=>'#user_first_name#, #task_name#',
                'message'=>'#user_first_name# has stop task the #task_name#',
                'created_at' => date('Y-m-d h:i:s'),
            ],
            [
                'action_id' => \Config::get('constants.FUNCTIONALITY_TYPE.TASK.MOVE-TASK'),
                'type'=>'1',
                'tags'=>'#user_first_name#, #task_name#',
                'message'=>'#user_first_name# has move task #task_name# from #from# to #to# section',
                'created_at' => date('Y-m-d h:i:s'),
            ],

        ]);
    }
}
