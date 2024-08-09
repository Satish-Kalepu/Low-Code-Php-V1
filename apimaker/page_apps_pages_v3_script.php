<script>
		
		document.getElementById('new-file-btn').addEventListener('click', function() {
			var configGlobalApimakerPath = "<?php echo $config_global_apimaker_path; ?>";
			var configPage = "<?php echo $config_page; ?>";
			var configParam1 = "<?php echo $config_param1; ?>";
			var configParam2 = "<?php echo $config_param2; ?>";
			var newPath = configGlobalApimakerPath + '/' + configPage + '/' + configParam1 + '/' + configParam2;
			window.location.href = newPath;
		});
				
		var app__ = <?=json_encode($app) ?>;
		var page__ = <?=json_encode($page) ?>;
		var vurls = {};

		var vurls_list = [];
		function set_preview_urls() {
			var urls = {};
			var ulist = [];
			console.log('dataaaa',page__)
			if ('cloud' in app__['settings']) {
				if (app__['settings']['cloud']) {
					var u = "https://" + app__['settings']['cloud-subdomain'] + '.' + app__['settings']['cloud-domain'] + '/' + app__['settings']['cloud-enginepath'] + page__.name;
					urls['cloud'] = u;
					ulist.push(u);
					if ('alias' in app__['settings']) {
						if (app__['settings']['alias']) {
							u = "https://" + app__['settings']['alias-domain'] + page__.name;
							urls['alias'] = u;
							ulist.push(u);
						}
					}
				}
			}
			if ('domains' in app__['settings']) {
				urls['domains'] = [];
				for (var d = 0; d < app__['settings']['domains'].length; d++) {
					u = app__['settings']['domains'][d]['url'] + page__.name;
					urls['domains'].push(u);
					ulist.push(u);
				}
			}
			vurls = urls;
			vurls_list = ulist;
				// console.log(vurls);
				// console.log(vurls_list);
		}
		function previewit() {
			set_preview_urls();
			var urlsListDiv = document.getElementById('urlsList');
			if (urlsListDiv) {
				urlsListDiv.innerHTML = ''; // Clear previous content
				// Add cloud and alias URLs if exist
				if (vurls['cloud']) {
					var p = document.createElement('p');
					var a = document.createElement('a');
					a.href = vurls['cloud'];
					a.target = '_blank';
					a.textContent = vurls['cloud'];
					p.appendChild(a);
					urlsListDiv.appendChild(p);
				}
				if (vurls['alias']) {
					var p = document.createElement('p');
					var a = document.createElement('a');
					a.href = vurls['alias'];
					a.target = '_blank';
					a.textContent = vurls['alias'];
					p.appendChild(a);
					urlsListDiv.appendChild(p);
				}
				// Add domain URLs if exist
				if (vurls['domains'] && vurls['domains'].length > 0) {
					for (var i = 0; i < vurls['domains'].length; i++) {
						var p = document.createElement('p');
						var a = document.createElement('a');
						a.href = vurls['domains'][i];
						a.target = '_blank';
						a.textContent = vurls['domains'][i];
						p.appendChild(a);
						urlsListDiv.appendChild(p);
					}
				}
				var url_modal = new bootstrap.Modal(document.getElementById('url_modal'));
				url_modal.show();
			} else {
				console.error("Element with id 'urlsList' not found.");
			}
		}

</script>
<div class="modal fade" id="url_modal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="url_modalLabel">Browse/Download File</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Custom Hosting: </p>
                <div id="urlsList"></div>
            </div>
        </div>
    </div>
</div>

