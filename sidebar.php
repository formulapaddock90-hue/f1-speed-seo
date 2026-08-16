<?php
/**
 * Sidebar Template
 *
 * @package F1_Speed_SEO
 */

$next_gp = f1_speed_seo_get_next_grand_prix();
?>

<aside class="home-sidebar" role="complementary">

    <?php if ( ! empty( $next_gp ) ) : ?>
        <div class="sidebar-widget f1-next-gp-widget">
            <div class="gp-widget-header">
                <span class="gp-round-badge">ROUND <?php echo esc_html( $next_gp['round'] ); ?></span>
                <h3 class="widget-title">🏁 <?php echo esc_html( $next_gp['race_name'] ); ?></h3>
                <?php if ( ! empty( $next_gp['circuit'] ) ) : ?>
                    <p class="gp-location">📍 <?php echo esc_html( $next_gp['circuit'] ); ?><?php echo ! empty( $next_gp['locality'] ) ? ' (' . esc_html( $next_gp['locality'] ) . ')' : ''; ?></p>
                <?php endif; ?>
            </div>

            <?php if ( ! empty( $next_gp['next_session'] ) ) : ?>
                <div class="f1-countdown-box">
                    <div class="cd-header">
                        <span class="cd-label" id="f1-cd-status-title">⏱️ PROSSIMA SESSIONE: <strong><?php echo esc_html( mb_strtoupper( $next_gp['next_session']['name'] ) ); ?></strong></span>
                    </div>

                    <div id="f1-countdown-timer" class="f1-countdown-grid" data-timestamp="<?php echo esc_attr( $next_gp['next_session']['timestamp'] ); ?>">
                        <div class="cd-item">
                            <span id="f1-cd-days" class="cd-val">00</span>
                            <span class="cd-unit">GIORNI</span>
                        </div>
                        <div class="cd-sep">:</div>
                        <div class="cd-item">
                            <span id="f1-cd-hours" class="cd-val">00</span>
                            <span class="cd-unit">ORE</span>
                        </div>
                        <div class="cd-sep">:</div>
                        <div class="cd-item">
                            <span id="f1-cd-mins" class="cd-val">00</span>
                            <span class="cd-unit">MIN</span>
                        </div>
                        <div class="cd-sep">:</div>
                        <div class="cd-item">
                            <span id="f1-cd-secs" class="cd-val">00</span>
                            <span class="cd-unit">SEC</span>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

            <?php if ( ! empty( $next_gp['sessions'] ) ) : ?>
                <div class="f1-schedule-list">
                    <h4 class="schedule-title">📅 Orari Sessioni (GMT+2)</h4>
                    <ul class="sessions-ul">
                        <?php foreach ( $next_gp['sessions'] as $session ) : 
                            $class = 'session-row';
                            if ( $session['is_next'] ) {
                                $class .= ' session-next';
                            } elseif ( $session['is_past'] ) {
                                $class .= ' session-past';
                            }
                        ?>
                            <li class="<?php echo esc_attr( $class ); ?>">
                                <div class="session-info">
                                    <span class="session-name">
                                        <?php if ( $session['is_next'] ) : ?>
                                            <span class="next-indicator">▶</span>
                                        <?php elseif ( $session['is_past'] ) : ?>
                                            <span class="past-indicator">✓</span>
                                        <?php endif; ?>
                                        <?php echo esc_html( $session['name'] ); ?>
                                    </span>
                                </div>
                                <div class="session-timing">
                                    <span class="session-date"><?php echo esc_html( $session['date_formatted'] ); ?></span>
                                    <span class="session-time"><?php echo esc_html( $session['time_formatted'] ); ?></span>
                                </div>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>
        </div>
    <?php endif; ?>

    <div class="sidebar-widget">
        <h3 class="widget-title">🏆 Classifica Piloti</h3>
        <div class="standings-table-wrap">
            <table class="standings-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Pilota</th>
                        <th>Team</th>
                        <th>Pt</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $drivers = f1_speed_seo_get_driver_standings();
                    foreach ( array_slice($drivers, 0, 12) as $d ) {
                        echo '<tr>';
                        echo '<td>' . esc_html($d['position']) . '</td>';
                        echo '<td><strong>' . esc_html($d['code'] ?: $d['name']) . '</strong></td>';
                        echo '<td class="team-col">' . esc_html($d['team']) . '</td>';
                        echo '<td>' . esc_html($d['points']) . '</td>';
                        echo '</tr>';
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>

    <div class="sidebar-widget">
        <h3 class="widget-title">🏎️ Classifica Costruttori</h3>
        <div class="standings-table-wrap">
            <table class="standings-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Team</th>
                        <th>Pt</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $constructors = f1_speed_seo_get_constructor_standings();
                    foreach ( $constructors as $c ) {
                        echo '<tr>';
                        echo '<td>' . esc_html($c['position']) . '</td>';
                        echo '<td><strong>' . esc_html($c['team']) . '</strong></td>';
                        echo '<td>' . esc_html($c['points']) . '</td>';
                        echo '</tr>';
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>

</aside>
