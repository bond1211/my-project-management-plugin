<?php
if ( ! is_user_logged_in() ) {
    wp_die( __( 'Войдите для доступа к задачам.', 'pmp' ) );
}
$task_id = isset( $_GET['pmp_task_id'] ) ? intval( $_GET['pmp_task_id'] ) : 0;
if ( ! $task_id ) {
    wp_die( __( 'Неверный идентификатор задачи.', 'pmp' ) );
}
global $wpdb;
$table_tasks = $wpdb->prefix . 'pmp_tasks';
$task = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM $table_tasks WHERE id = %d", $task_id ) );
if ( ! $task ) {
    wp_die( __( 'Задача не найдена', 'pmp' ) );
}
$executors = json_decode( $task->executors, true );
?>
<div class="pmp-frontend-task-page">
    <h2>Задача: <?php echo esc_html( $task->task_name ); ?></h2>
    <p><strong>Описание:</strong> <?php echo esc_html( $task->description ); ?></p>
    <p><strong>Уровень срочности:</strong> <?php echo esc_html( $task->urgency_level ); ?></p>
    <p><strong>Статус:</strong> <?php echo esc_html( $task->status ); ?></p>
    <p><strong>Исполнители:</strong> <?php echo is_array($executors) ? implode(', ', $executors) : ''; ?></p>
    <p><strong>Даты:</strong></p>
    <ul>
        <li>Получения задания: <?php echo esc_html( $task->received_date ); ?></li>
        <li>Начало работы: <?php echo esc_html( $task->start_date ); ?></li>
        <li>Фактическое выполнение: <?php echo esc_html( $task->actual_finish_date ); ?></li>
        <li>Выдача на проверку: <?php echo esc_html( $task->review_date ); ?></li>
        <li>Выдача ГИПу: <?php echo esc_html( $task->gip_date ); ?></li>
    </ul>
    
    <?php if ( current_user_can( 'pmp_manage_tasks' ) && $task->status != 'closed' ) : ?>
    <form id="pmp-close-task-form">
         <input type="hidden" name="task_id" value="<?php echo $task->id; ?>" />
         <?php wp_nonce_field( 'pmp_frontend_nonce', 'nonce' ); ?>
         <button type="submit">Закрыть задачу</button>
    </form>
    <div id="pmp-close-task-response"></div>
    <?php endif; ?>
    
    <!-- Секция переписки для задачи -->
    <div class="pmp-task-messaging" style="margin-top:20px; border:1px solid #ccc; padding:10px;">
         <h3>Переписка по задаче</h3>
         <div id="pmp-task-messages">
             <?php
             $args = array(
                 'post_type'  => 'pmp_message',
                 'meta_query' => array(
                     array(
                         'key'   => 'object_type',
                         'value' => 'task',
                     ),
                     array(
                         'key'   => 'object_id',
                         'value' => $task->id,
                     )
                 )
             );
             $messages = get_posts( $args );
             if ( $messages ) {
                 foreach ( $messages as $msg ) {
                     echo '<div class="pmp-message" style="border-bottom:1px dashed #ccc; padding:5px 0;">';
                     echo '<p>' . esc_html( $msg->post_content ) . '</p>';
                     echo '<small>Дата: ' . esc_html( $msg->post_date ) . '</small>';
                     echo '</div>';
                 }
             } else {
                 echo '<p>Нет сообщений.</p>';
             }
             ?>
         </div>
         <form id="pmp-task-message-form">
             <input type="hidden" name="object_type" value="task" />
             <input type="hidden" name="object_id" value="<?php echo $task->id; ?>" />
             <?php wp_nonce_field( 'pmp_message_nonce', 'msg_nonce' ); ?>
             <textarea name="message" placeholder="Ваше сообщение" style="width:100%;" required></textarea><br/>
             <button type="submit">Отправить сообщение</button>
         </form>
         <div id="pmp-task-message-response"></div>
    </div>
    
    <p><a href="<?php echo esc_url( remove_query_arg( array('pmp_task_id','pmp_project_id') ) ); ?>">Вернуться к проекту</a></p>
</div>