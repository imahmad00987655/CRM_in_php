$(document).ready(function () {
	ensurePanelPage();
});

function ensurePanelPage() {
	const panelPath = '/panel.php';
	if (window.location.pathname !== panelPath) {
		window.location.replace(panelPath);
		return;
	}
	if(userRole && userRole != 'sales_agent'){
		initDashboard();
	}else{
		loadComponent('applications.php', 'Applications', false);
		window.loadComponent = loadComponent;
	}
}

function initDashboard() {
	const lastPage = localStorage.getItem('lastPage') || 'dashboard.php';
	const lastTitle = localStorage.getItem('lastTitle') || 'Dashboard';

	loadComponent(lastPage, lastTitle, false);

	window.onpopstate = (event) => {
		if (event.state?.page) {
			loadComponent(event.state.page, event.state.title, false);
		}
	};

	window.loadComponent = loadComponent;
}

function loadComponent(page, title, updateHistory = true, extraData = {}) {
	if ($('#dashboard-content').attr('data-current-page') === page) return;

	$.ajax({
		url: `pages/${page}`,
		method: 'GET',
		cache: false,
		data: { user_role: userRole }, // 🔹 Sending user_role
		dataType: 'html', // 🔹 Ensure response is treated as HTML
		success: (response) => {
			$('#dashboard-content').hide().html(response).fadeIn(200);
			$('#topBarTitle').text(title);

			if (updateHistory) {
				history.pushState({ page, title }, title, window.location.pathname);
			}

			document.title = `${title} - Panel`;
			localStorage.setItem('lastPage', page);
			localStorage.setItem('lastTitle', title);

			// 🔹 If loading `newApplication.php`, fetch application data and update form
			if (page === 'newApplication.php') {
				$.getScript('../js/new_application.js', function () {
					setTimeout(() => {
						setFormDefaults(extraData);
						initializeArrayToFormData();
						bindExpandableListeners();
						restrictEditing(extraData);
					}, 100);
				});
			}
			if (page === 'newOfficer.php') {
				$.getScript('../js/new_officer.js', function () {
					setTimeout(() => {
						setOfficersData(extraData);
					}, 1000);
				});
			}
			if (page === 'dashboard.php') {
				fetchApplications();
				chartRegistry = {};
			}
			if (page === 'applications.php') fetchData();
			if (page === 'billing.php') fetchData();
			if (page === 'officers.php') fetchData();
		},
		error: () => $('#dashboard-content').html('<p>Error loading content.</p>'),
	});
}
