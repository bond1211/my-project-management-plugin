<?php
if ( ! is_user_logged_in() ) {
    wp_die( __( 'Войдите для доступа к панели проектов.', 'pmp' ) );
}
global $wpdb;
$user_id = get_current_user_id();
$table = $wpdb->prefix . 'pmp_projects';
// Выбираем проекты, где пользователь либо создал, либо ему назначен
$projects = $wpdb->get_results( $wpdb->prepare(
    "SELECT * FROM $table WHERE assigned_to = %d OR created_by = %d",
    $user_id, $user_id
) );
// Подсчитываем непринятые проекты (назначенные, accepted = 0)
$unaccepted_count = $wpdb->get_var( $wpdb->prepare(
    "SELECT COUNT(*) FROM $table WHERE assigned_to = %d AND accepted = 0",
    $user_id
) );

$closed_projects  = array();
$current_projects = array();
$future_projects  = array();

if ( $projects ) {
    foreach( $projects as $project ) {
        if( $project->status == 'closed' ) {
            $closed_projects[] = $project;
        } elseif( $project->status == 'future' ) {
            $future_projects[] = $project;
        } else {
            $current_projects[] = $project;
        }
    }
}
?>
<div class="pmp-frontend-dashboard">
    <div class="pmp-top-panel" style="background:#f1f1f1; padding:10px; margin-bottom:10px;">
        <span>Непринятые задания: <?php echo intval($unaccepted_count); ?></span>
        <span>Активные задания: 0</span>
        <span>Просроченные задания: 0</span>
    </div>
    <div class="pmp-columns" style="display:flex; gap:10px;">
        <!-- Левая колонка: закрытые проекты -->
        <div class="pmp-column pmp-left" style="width:15%;">
            <h3>Закрытые проекты</h3>
            <input type="text" id="pmp-search-closed" placeholder="Поиск" style="width:100%; margin-bottom:5px;" />
            <div id="pmp-closed-projects" style="overflow-y:auto; height:300px; border:1px solid #ccc; padding:5px;">
                <?php if ( ! empty( $closed_projects ) ): ?>
                    <ul>
                    <?php foreach ( $closed_projects as $project ): ?>
                        <li>
                            <a href="<?php echo esc_url( add_query_arg( 'pmp_project_id', $project->id ) ); ?>">
                                <?php echo esc_html( $project->project_name ); ?>
                            </a>
                        </li>
                    <?php endforeach; ?>
                    </ul>
                <?php else: ?>
                    <p>Нет закрытых проектов.</p>
                <?php endif; ?>
            </div>
        </div>
        
        <!-- Центральная колонка: текущие проекты и форма создания проекта -->
        <div class="pmp-column pmp-central" style="width:70%;">
            <h3>Текущие проекты</h3>
            <?php if ( current_user_can( 'pmp_create_project' ) ) : ?>
            <div class="pmp-create-project" style="border:1px solid #ccc; padding:10px; margin-bottom:10px;">
                <h4>Добавить проект</h4>
                <form id="pmp-create-project-form">
                    <label for="project_name">Название проекта:</label><br/>
                    <input type="text" id="project_name" name="project_name" style="width:100%;" required /><br/><br/>
                    
                    <label for="description">Описание проекта:</label><br/>
                    <textarea id="description" name="description" style="width:100%;" required></textarea><br/><br/>
                    
                    <label for="deadline">Срок:</label><br/>
                    <input type="datetime-local" id="deadline" name="deadline" style="width:100%;" required /><br/><br/>
                    
                    <label for="urgency_level">Уровень срочности:</label><br/>
                    <select id="urgency_level" name="urgency_level" style="width:100%;">
                        <option value="critical">Критический</option>
                        <option value="high">Высокий</option>
                        <option value="normal">Нормальный</option>
                    </select><br/><br/>
                    
                    <?php wp_nonce_field( 'pmp_frontend_nonce', 'nonce' ); ?>
                    <button type="submit">Создать проект</button>
                </form>
                <div id="pmp-create-project-response"></div>
            </div>
            <?php endif; ?>
            
            <div id="pmp-current-projects" style="border:1px solid #ccc; padding:5px;">
                <?php if ( ! empty( $current_projects ) ): ?>
                    <ul>
                    <?php foreach ( $current_projects as $project ): ?>
                        <li>
                            <a href="<?php echo esc_url( add_query_arg( 'pmp_project_id', $project->id ) ); ?>">
                                <?php echo esc_html( $project->project_name ); ?>
                            </a>
                        </li>
                    <?php endforeach; ?>
                    </ul>
                <?php else: ?>
                    <p>Нет текущих проектов.</p>
                <?php endif; ?>
            </div>
        </div>
        
        <!-- Правая колонка: будущие проекты -->
        <div class="pmp-column pmp-right" style="width:15%;">
            <h3>Будущие проекты</h3>
            <input type="text" id="pmp-search-future" placeholder="Поиск" style="width:100%; margin-bottom:5px;" />
            <div id="pmp-future-projects" style="overflow-y:auto; height:300px; border:1px solid #ccc; padding:5px;">
                <?php if ( ! empty( $future_projects ) ): ?>
                    <ul>
                    <?php foreach ( $future_projects as $project ): ?>
                        <li>
                            <a href="<?php echo esc_url( add_query_arg( 'pmp_project_id', $project->id ) ); ?>">
                                <?php echo esc_html( $project->project_name ); ?>
                            </a>
                        </li>
                    <?php endforeach; ?>
                    </ul>
                <?php else: ?>
                    <p>Нет будущих проектов.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>