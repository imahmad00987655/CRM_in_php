var formid = null;
fetchOptions('cities', 'officer_city');
fetchOptions('roles', 'officer_user_role');

function setOfficersData(fetcheddata) {

	if (!fetcheddata || typeof fetcheddata !== 'object') return; // Ensure data is valid
	formid = fetcheddata.id;
	Object.keys(fetcheddata).forEach((key) => {
		const element = document.getElementById('officer_' + key);
		if (element) {
			element.value = fetcheddata[key]; // Set value for inputs
		}
	});
	// Explicitly set role_id to officer_user_role
	const roleElement = document.getElementById('officer_user_role');
	console.log('role id',fetcheddata.role_id);
	if (roleElement && fetcheddata.role_id) {
		roleElement.value = fetcheddata.role_id;
	}
	const cityElement = document.getElementById('officer_city');
	if (cityElement && fetcheddata.city_id) {
		cityElement.value = fetcheddata.city_id;
	}
}

function fetchOptions(type, elementId, selectedValue) {
	fetch(`../backend/officers_controller.php?fetch=${type}`)
		.then((response) => response.json())
		.then((data) => {
			const select = document.getElementById(elementId);
			select.innerHTML = `<option>Select ${type.charAt(0).toUpperCase() + type.slice(1)}</option>`;
			data.forEach((item) => {
				const itemName = type == 'roles' ? item.role_name : item.city_name;
				let formattedName = itemName
					.replace(/_/g, ' ') // Replace underscores with spaces
					.replace(/\b\w/g, (char) => char.toUpperCase()); // Capitalize each word
				let isSelected = itemName === selectedValue ? 'selected' : '';
				select.innerHTML += `<option value="${item.id}" ${isSelected}>${formattedName}</option>`;
			});
		})
		.catch((error) => console.error(`Error fetching ${type}:`, error));
}

function saveOrUpdateOfficer() {
	const id = formid ?? null;
	const name = document.getElementById('officer_name').value.trim();
	const email = document.getElementById('officer_email').value.trim();
	const phone_number = document.getElementById('officer_phone_number').value.trim();
	const cnic = document.getElementById('officer_cnic').value.trim();
	const user_role = document.getElementById('officer_user_role').value;
	const date_of_birth = document.getElementById('officer_date_of_birth').value;
	const gender = document.getElementById('officer_gender').value;
	const city = document.getElementById('officer_city').value;
	const username = document.getElementById('officer_username').value.trim();
	const password = document.getElementById('officer_password').value;
	// Validation checks
	if (!name || !email || !user_role || !username || (formid && !id) || (!formid && !password) || !city || !gender || !date_of_birth || !cnic) {
		alert('Please fill in all required fields.');
		return;
	}

	const data = {
		id,
		name,
		email,
		phone_number,
		cnic,
		user_role,
		date_of_birth,
		gender,
		city,
		username,
		password, // Only send password for new users
	};

	fetch(`../backend/officers_controller.php?id=${id}`, {
		method: id ? 'PUT' : 'POST',
		headers: {
			'Content-Type': 'application/json',
		},
		body: JSON.stringify(data),
	})
		.then((response) => response.json())
		.then((result) => {
			clearForm();
			alert(result.message);
		})
		.catch((error) => console.error('Error submitting officer:', error));
}

function clearForm() {
	document.querySelectorAll('input, select, textarea').forEach((element) => {
		if (element.tagName === 'SELECT') {
			element.selectedIndex = 0; // Reset select dropdown to first option
		} else {
			element.value = ''; // Clear input and textarea fields
		}
	});
}
