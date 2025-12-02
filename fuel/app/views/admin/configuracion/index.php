<div class="container-fluid py-4">
	<div class="row">
		<div class="col-12">
			<div class="card">
				<div class="card-header pb-0">
					<div class="d-flex align-items-center">
						<h5 class="mb-0">Configuración del Sitio</h5>
					</div>
					<p class="text-sm mb-0">Gestiona la configuración general, SEO, tracking y privacidad del sitio.</p>
				</div>
				<div class="card-body">
					
					<!-- Nav Tabs -->
					<ul class="nav nav-tabs mb-4" id="configTabs" role="tablist">
						<li class="nav-item" role="presentation">
							<button class="nav-link <?php echo ($active_tab === 'general') ? 'active' : ''; ?>" 
								id="general-tab" 
								data-bs-toggle="tab" 
								data-bs-target="#general" 
								type="button" 
								role="tab">
								<i class="fas fa-cog me-1"></i> General
							</button>
						</li>
						<li class="nav-item" role="presentation">
							<button class="nav-link <?php echo ($active_tab === 'seo') ? 'active' : ''; ?>" 
								id="seo-tab" 
								data-bs-toggle="tab" 
								data-bs-target="#seo" 
								type="button" 
								role="tab">
								<i class="fas fa-search me-1"></i> SEO
							</button>
						</li>
						<li class="nav-item" role="presentation">
							<button class="nav-link <?php echo ($active_tab === 'tracking') ? 'active' : ''; ?>" 
								id="tracking-tab" 
								data-bs-toggle="tab" 
								data-bs-target="#tracking" 
								type="button" 
								role="tab">
								<i class="fas fa-chart-line me-1"></i> Tracking
							</button>
						</li>
						<li class="nav-item" role="presentation">
							<button class="nav-link <?php echo ($active_tab === 'cookies') ? 'active' : ''; ?>" 
								id="cookies-tab" 
								data-bs-toggle="tab" 
								data-bs-target="#cookies" 
								type="button" 
								role="tab">
								<i class="fas fa-cookie-bite me-1"></i> Cookies
							</button>
						</li>
						<li class="nav-item" role="presentation">
							<button class="nav-link <?php echo ($active_tab === 'favicons') ? 'active' : ''; ?>" 
								id="favicons-tab" 
								data-bs-toggle="tab" 
								data-bs-target="#favicons" 
								type="button" 
								role="tab">
								<i class="fas fa-image me-1"></i> Favicons
							</button>
						</li>
						<li class="nav-item" role="presentation">
							<button class="nav-link <?php echo ($active_tab === 'scripts') ? 'active' : ''; ?>" 
								id="scripts-tab" 
								data-bs-toggle="tab" 
								data-bs-target="#scripts" 
								type="button" 
								role="tab">
								<i class="fas fa-code me-1"></i> Scripts
							</button>
						</li>
					</ul>

					<!-- Tab Content -->
					<div class="tab-content" id="configTabContent">
						
						<!-- GENERAL TAB -->
						<div class="tab-pane fade <?php echo ($active_tab === 'general') ? 'show active' : ''; ?>" 
							id="general" 
							role="tabpanel">
							<?php echo View::forge('admin/configuracion/tabs/general', array('config' => $config)); ?>
						</div>

						<!-- SEO TAB -->
						<div class="tab-pane fade <?php echo ($active_tab === 'seo') ? 'show active' : ''; ?>" 
							id="seo" 
							role="tabpanel">
							<?php echo View::forge('admin/configuracion/tabs/seo', array('config' => $config)); ?>
						</div>

						<!-- TRACKING TAB -->
						<div class="tab-pane fade <?php echo ($active_tab === 'tracking') ? 'show active' : ''; ?>" 
							id="tracking" 
							role="tabpanel">
							<?php echo View::forge('admin/configuracion/tabs/tracking', array('config' => $config)); ?>
						</div>

						<!-- COOKIES TAB -->
						<div class="tab-pane fade <?php echo ($active_tab === 'cookies') ? 'show active' : ''; ?>" 
							id="cookies" 
							role="tabpanel">
							<?php echo View::forge('admin/configuracion/tabs/cookies', array('config' => $config)); ?>
						</div>

						<!-- FAVICONS TAB -->
						<div class="tab-pane fade <?php echo ($active_tab === 'favicons') ? 'show active' : ''; ?>" 
							id="favicons" 
							role="tabpanel">
							<?php echo View::forge('admin/configuracion/tabs/favicons', array('config' => $config)); ?>
						</div>

						<!-- SCRIPTS TAB -->
						<div class="tab-pane fade <?php echo ($active_tab === 'scripts') ? 'show active' : ''; ?>" 
							id="scripts" 
							role="tabpanel">
							<?php echo View::forge('admin/configuracion/tabs/scripts', array('config' => $config)); ?>
						</div>

					</div>

				</div>
			</div>
		</div>
	</div>
</div>

<script>
// Mantener el tab activo después de submit
document.addEventListener('DOMContentLoaded', function() {
	const urlParams = new URLSearchParams(window.location.search);
	const activeTab = urlParams.get('tab');
	
	if (activeTab) {
		const tabButton = document.querySelector(`#${activeTab}-tab`);
		if (tabButton) {
			const tab = new bootstrap.Tab(tabButton);
			tab.show();
		}
	}
});
</script>
