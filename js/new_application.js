var formData = {
	id: null,
	applicant_name: '',
	applicant_surname: '',
	phone_number: '',
	total_amount: 0,
	applicant_cnic: '',
	passport_number: '',
	application_country_id: '',
	application_city: '',
	occupation: '',
	persons: '',
	visa_agent: '',
	data_entry_agent: '',
	proceed_to_agent: '0',
	applicant_address: '',
	// application_limit: '',
	deadline_date: '',
	person_1: null,
	person_2: null,
	person_3: null,
	person_4: null,
	traveling_plan: [],
	extra_info: '',
	status: 'Pending',
	advance_amount: 0,
};
var formFields = document.querySelectorAll('input, select'); // Include both inputs and select elements
var isFetchedDocument = false;

function setFormDefaults(fetcheddata) {
	if (fetcheddata && Object.keys(fetcheddata).length > 0) {
		formData = fetcheddata;
		isFetchedDocument = true;
	}
	formFields.forEach((field) => {
		const fieldName = field.name;
		if (field.type === 'radio') {
			if (formData[fieldName] === field.value) {
				field.checked = true;
			}
		} else if (field.type === 'checkbox') {
			field.checked = formData[fieldName] || false;
		} else if (field.tagName.toLowerCase() === 'select') {
			// For select dropdowns
			if (formData[fieldName]) {
				field.value = formData[fieldName];
			}
		} else {
			if (formData.hasOwnProperty(fieldName)) {
				field.value = formData[fieldName];
			}
		}
	});

	// formData.application_city &&  fetchAgentsAndDataEntries(formData.application_city);

	if(formData.application_city){
		setTimeout(function() {
			fetchAgentsAndDataEntries(formData.application_city);
		}, 1000);
	}

	if (isFetchedDocument && formData.application_country_id) {
		generatePersons();
	}
}
function handleInputChange(event) {
	const { name, value, type, checked } = event.target;
	if (type === 'checkbox') {
		formData[name] = checked;
	} else if (type === 'radio') {
		if (checked) {
			formData[name] = value;
		}
	} else {
		formData[name] = value;
	}
	// Handle changes based on specific input fields
	if (name === 'application_city') {
		fetchAgentsAndDataEntries(formData.application_city);
	}
	if (name === 'persons') {
		// fetchAmountBasedOnSelection();
		generatePersons();
	}
}
formFields.forEach((field) => {
	field.addEventListener('input', handleInputChange);
});
//
//
//
// Fetch agents and data entry agents and documents
function fetchAgentsAndDataEntries(cityId) {
	if (!cityId) {
		document.getElementById('visa_agent').innerHTML = '<option value="">Choose option</option>';
		document.getElementById('data_entry_agent').innerHTML = '<option value="">Choose option</option>';
		return;
	}

	var xhr = new XMLHttpRequest();
	xhr.open('GET', '../backend/fetch_agents.php?city=' + encodeURIComponent(cityId), true);
	xhr.onreadystatechange = function () {
		if (xhr.readyState === 4) {
			if (xhr.status === 200) {
				try {
					var data = xhr.responseText ? JSON.parse(xhr.responseText) : {};
					var agentOptions = '<option value="">Choose option</option>';
					var dataEntryOptions = '<option value="">Choose option</option>';

					if (Array.isArray(data.agents) && data.agents.length > 0) {
						// Add agent options and set the first one as default selected
						data.agents.forEach((agent) => {
							agentOptions += `<option value="${agent.id}" ${agent.id === Number(formData.visa_agent) ? 'selected' : ''}>${agent.name}</option>`;
						});
					}

					if (Array.isArray(data.data_entry_agents) && data.data_entry_agents.length > 0) {
						// Add data entry agent options and set the first one as default selected
						data.data_entry_agents.forEach((entry) => {
							dataEntryOptions += `<option value="${entry.id}" ${entry.id === Number(formData.data_entry_agent) ? 'selected' : ''}>${
								entry.name
							}</option>`;
						});
					}

					document.getElementById('visa_agent').innerHTML = agentOptions;
					document.getElementById('data_entry_agent').innerHTML = dataEntryOptions;
				} catch (error) {
					console.error('Error parsing JSON response: ', error);
				}
			} else {
				console.error('Error fetching data: ' + xhr.status);
			}
		}
	};

	xhr.send();
}
function fetchAmountBasedOnSelection() {
	const persons = formData.persons;
	const countryId = formData.application_country_id;
	var amountInput = document.getElementById('total_amount');
	// ✅ Validate input
	if (!persons) {
		alert('Please select the number of persons first.');
		amountInput.value = ''; // Clear input field
		return;
	}
	if (!countryId) {
		alert('Please select a country first.');
		amountInput.value = ''; // Clear input field
		return;
	}

	var xhr = new XMLHttpRequest();
	xhr.open('GET', '../backend/fetch_amount.php?persons=' + encodeURIComponent(persons) + '&country_id=' + encodeURIComponent(countryId), true);
	xhr.onreadystatechange = function () {
		if (xhr.readyState == 4) {
			if (xhr.status == 200) {
				try {
					var response = JSON.parse(xhr.responseText);
					if (response.amount) {
						let amountInput = document.getElementById('total_amount');
						let minValue = response.amount * 0.5; // 50% of fetched amount

						// ✅ Store original and minimum values
						amountInput.dataset.originalAmount = response.amount;
						amountInput.dataset.minAmount = minValue;

						// ✅ Set default value
						amountInput.value = response.amount;
						formData['total_amount'] = response.amount;
					} else {
						alert(response.error || 'No data found.');
					}
				} catch (error) {
					console.error('Invalid JSON response', error);
				}
			} else {
				alert('Error fetching data.');
			}
		}
	};
	xhr.send();

	amountInput.addEventListener('blur', function () {
		let minValue = parseFloat(this.dataset.minAmount) || 0;
		let currentValue = parseFloat(this.value);

		if (currentValue < minValue) {
			alert(`Amount cannot be less than 50% of the original amount (${minValue}).`);
			this.value = this.dataset.originalAmount; // Reset to original amount
		}
	});
}

//
//
//
// generate persons
function generatePersons() {
	if (!isFetchedDocument) {
		formData['person_1'] = null;
		formData['person_2'] = null;
		formData['person_3'] = null;
		formData['person_4'] = null;
	}

	// fetchDocuments();
	const container = document.getElementById('personContainer');
	container.innerHTML = ''; // Clear existing
	// Determine number of persons
	const personCount = formData.persons === 'couple' ? 2 : formData.persons === 'family-3' ? 3 : formData.persons === 'family-4' ? 4 : 1;

	// Create tab container
	const tabs = document.createElement('div');
	tabs.classList.add('flex', 'gap-2', 'mb-4');

	// Create content container
	const contentContainer = document.createElement('div');

	for (let i = 1; i <= personCount; i++) {
		if (typeof formData[`person_${i}`] === 'string' && formData[`person_${i}`] !== null) {
			try {
				formData[`person_${i}`] = JSON.parse(formData[`person_${i}`]);
			} catch (e) {
				formData[`person_${i}`] = { name: `Person ${i}` }; // Reset to empty object if JSON parsing fails
			}
		}
		if (formData[`person_${i}`] === null) {
			formData[`person_${i}`] = { name: `Person ${i}` };
		}

		const personId = `person_${i}`;

		// Tab button
		const tab = document.createElement('div');
		tab.classList.add('py-2', 'px-4', 'rounded-lg', 'text-2xl', 'bg-white', 'text-red-600', 'cursor-pointer', 'border', 'person-tab');
		tab.innerText = formData[personId].name;
		tab.onclick = () => showPersonContent(i);
		tab.id = `tab-${i}`;
		// Edit tab name
		tab.ondblclick = () => {
			const input = document.createElement('input');
			input.type = 'text';
			input.value = formData[personId].name;
			input.classList.add('border', 'p-1', 'rounded', 'w-full', 'outline-none', 'text-black');

			input.onblur = () => {
				formData[personId].name = input.value.trim() || `Person ${i}`;
				tab.innerText = formData[personId].name; // Restore text
			};

			tab.innerHTML = ''; // Clear the tab content completely
			tab.appendChild(input);
			input.focus();
		};

		tabs.appendChild(tab);

		// Form container
		const personDiv = generatePersonContent(i, personId);
		contentContainer.appendChild(personDiv);
	}
	container.appendChild(tabs);
	container.appendChild(contentContainer);
	// Show first person form by default
	showPersonContent(1);
	function showPersonContent(index) {
		document.querySelectorAll('[id^="person-content-"]').forEach((section) => section.classList.add('hidden'));
		document.getElementById(`person-content-${index}`).classList.remove('hidden');
		// Tab Styling
		document.querySelectorAll('.person-tab').forEach((tab) => {
			tab.classList.remove('bg-red-600', 'text-white');
			tab.classList.add('bg-white', 'text-red-600');
		});
		document.getElementById(`tab-${index}`).classList.remove('bg-white', 'text-red-600');
		document.getElementById(`tab-${index}`).classList.add('bg-red-600', 'text-white');
	}
}
function generatePersonContent(index, personId) {
	const personDiv = document.createElement('div');
	personDiv.classList.add('hidden', 'w-full', 'flex', 'flex-col', 'gap-5');
	personDiv.id = `person-content-${index}`;
	// Generate Document Checkboxes
	const mandatoryDocsContainer = generatePersonDocuments(personId);
	personDiv.appendChild(mandatoryDocsContainer);
	// generate optional documents
	const optDocsContainer = generateOptionalDetails(personId);
	personDiv.appendChild(optDocsContainer);
	// job persona
	const jobDocsContainer = generateJobPersona(personId);
	personDiv.appendChild(jobDocsContainer);
	// educational Docs
	const eduDataContainer = generateEducationalDetails(personId);
	personDiv.appendChild(eduDataContainer);
	// generate social data
	const socialDataContainer = generateSocialDetails(personId);
	personDiv.appendChild(socialDataContainer);
	// // generate Parent info
	const parentDataContainer = generateParentsInfo(personId);
	personDiv.appendChild(parentDataContainer);
	// // generate Refusal info
	const refusalDataContainer = generateRefusalInfo(personId);
	personDiv.appendChild(refusalDataContainer);
	// // generate Other info
	const otherDataContainer = generateOtherInfo(personId);
	personDiv.appendChild(otherDataContainer);
	// generate 3 Month History info
	const threeMonthDataContainer = generateThreeMonthTripDetails(personId);
	personDiv.appendChild(threeMonthDataContainer);
	// generate 6 Month History info
	const sixMonthDataContainer = generateSixMonthTripDetails(personId);
	personDiv.appendChild(sixMonthDataContainer);
	// generate Employement info
	const employDataContainer = createEmploymentHistoryForm(personId);
	personDiv.appendChild(employDataContainer);

	return personDiv;
}

//
//
//
//  generate persons data
function generatePersonDocuments(personId) {
	const countryId = formData.application_country_id;

	// Create the container upfront
	const checkboxContainer = document.createElement('div');
	checkboxContainer.classList.add('h-fit', 'w-full', 'flex', 'flex-col', 'gap-2', 'p-5', 'bg-white', 'bg-opacity-50', 'rounded-xl');

	const heading = document.createElement('div');
	heading.innerText = 'Mandatory Documents';
	heading.classList.add('text-red-600', 'text-3xl', 'font-bold', 'mb-5');
	checkboxContainer.appendChild(heading);

	// Fetch documents and populate the container
	fetch(`../backend/visainformationportal.php?action=get_documents&parent_id=${countryId}`)
		.then((response) => response.json())
		.then((data) => {
			if (!Array.isArray(data)) {
				throw new Error('Invalid document data');
			}

			data.forEach((doc, docIndex) => {
				const checkboxWrapper = document.createElement('div');
				checkboxWrapper.classList.add('flex', 'text-wrap', 'items-center', 'justify-between', 'gap-4');

				const label = document.createElement('label');
				label.innerText = `${docIndex + 1}. ${doc.document}`;
				label.classList.add('text-lg', 'w-full', 'text-wrap');

				const checkboxInput = document.createElement('input');
				checkboxInput.type = 'checkbox';
				checkboxInput.name = `${personId}_${doc.document}`;
				checkboxInput.classList.add('h-5', 'w-5');
				checkboxInput.checked = formData[personId]?.mandatory_docs?.[doc.id] || false;

				checkboxInput.addEventListener('change', function () {
					if (!formData[personId].mandatory_docs) {
						formData[personId].mandatory_docs = {};
					}
					formData[personId].mandatory_docs[doc.id] = this.checked;
				});

				checkboxWrapper.appendChild(label);
				checkboxWrapper.appendChild(checkboxInput);
				checkboxContainer.appendChild(checkboxWrapper);
			});
		})
		.catch((error) => {
			console.error('Error fetching documents:', error);
			const errorMsg = document.createElement('p');
			errorMsg.classList.add('text-red-500');
			errorMsg.innerText = 'Failed to load documents.';
			checkboxContainer.appendChild(errorMsg);
		});

	return checkboxContainer;
}

function generateJobPersona(personId) {
	const optDocsDiv = document.createElement('div');
	optDocsDiv.classList.add('h-fit', 'w-full', 'flex', 'flex-col', 'gap-2', 'bg-white', 'bg-opacity-50', 'rounded-xl');
	// Expandable heading
	const heading = document.createElement('div');
	heading.id = `expandable-${personId + 'job'}`;
	heading.innerText = '+  Job Persons';
	heading.classList.add('w-full', 'text-3xl', 'font-bold', 'flex', 'gap-2', 'p-5', 'text-red-600', 'select-none', 'cursor-pointer');
	// Expandable content
	const contentDiv = document.createElement('div');
	contentDiv.id = `expandable-content-${personId + 'job'}`;
	contentDiv.classList.add('hidden', 'p-5', 'flex-col', 'w-full', 'h-fit', 'font-medium');

	// List of optional documents
	const optionalDocs = [
		{ id: 'noc_doc', label: 'NOC' },
		{ id: 'salary_slips', label: 'Salary Slips (Last 6 Months)' },
	];

	// Ensure formData[personId] exists
	if (!formData[personId]) {
		formData[personId] = { job_persona: {} };
	}

	optionalDocs.forEach((doc) => {
		const docWrapper = document.createElement('div');
		docWrapper.classList.add('flex', 'text-wrap', 'w-full', 'sm:w-1/2', 'mb-2', 'items-center', 'justify-between');

		const label = document.createElement('label');
		label.setAttribute('for', `${personId}_${doc.id}`);
		label.classList.add('text-xl', 'text-wrap');
		label.innerText = doc.label;

		const checkbox = document.createElement('input');
		checkbox.type = 'checkbox';
		checkbox.name = `${personId}_${doc.label}`;
		checkbox.id = `${personId}_${doc.id}`;
		checkbox.classList.add('h-5', 'w-5');
		checkbox.checked = formData[personId].job_persona?.[doc.id] || false;

		checkbox.addEventListener('change', function () {
			// Ensure documents object exists
			if (!formData[personId].job_persona) {
				formData[personId].job_persona = {};
			}
			// Update the document selection
			formData[personId].job_persona[doc.id] = this.checked;
		});

		docWrapper.appendChild(label);
		docWrapper.appendChild(checkbox);
		contentDiv.appendChild(docWrapper);
	});

	optDocsDiv.appendChild(heading);
	optDocsDiv.appendChild(contentDiv);
	return optDocsDiv;
}
function generateOptionalDetails(personId) {
	const optDetailsDiv = document.createElement('div');
	optDetailsDiv.classList.add('h-fit', 'w-full', 'flex', 'flex-col', 'gap-2', 'bg-white', 'bg-opacity-50', 'rounded-xl');

	// Unique expandable heading ID based on personId
	const heading = document.createElement('div');
	heading.id = `expandable-${personId + 'optional'}`;
	heading.innerText = '+  Optional Details';
	heading.classList.add('w-full', 'text-3xl', 'font-bold', 'flex', 'gap-2', 'p-5', 'text-red-600', 'select-none', 'cursor-pointer');

	// Expandable content div
	const contentDiv = document.createElement('div');
	contentDiv.id = `expandable-content-${personId + 'optional'}`;
	contentDiv.classList.add('hidden', 'p-5', 'flex-col', 'w-full', 'h-fit', 'font-medium', 'space-y-3');

	// List of optional details
	const optionalDetails = [
		{ id: 'property_documents_translated', label: 'Property Documents Translated' },
		{ id: 'credit_card_statement', label: 'Credit Card Statement 3 Months' },
		{ id: 'invitation_letter', label: 'Invitation Letter' },
		{ id: 'car_registration', label: 'Car Registration' },
	];

	// Ensure formData[personId] exists
	if (!formData[personId]) {
		formData[personId] = { optional_details: {} };
	}

	optionalDetails.forEach((doc) => {
		const docWrapper = document.createElement('div');
		docWrapper.classList.add('flex', 'text-wrap', 'w-full', 'sm:w-1/2', 'items-center', 'justify-between');

		const label = document.createElement('label');
		label.setAttribute('for', `${personId}_${doc.id}`);
		label.classList.add('text-xl');
		label.innerText = doc.label;

		const checkbox = document.createElement('input');
		checkbox.type = 'checkbox';
		checkbox.name = `${personId}_${doc.label}`;
		checkbox.id = `${personId}_${doc.id}`;
		checkbox.classList.add('h-5', 'w-5');
		checkbox.checked = formData[personId].optional_details?.[doc.id] || false;

		checkbox.addEventListener('change', function () {
			// Ensure the optional_details object exists for this person
			if (!formData[personId].optional_details) {
				formData[personId].optional_details = {};
			}
			// Update selection
			formData[personId].optional_details[doc.id] = this.checked;
		});

		docWrapper.appendChild(label);
		docWrapper.appendChild(checkbox);
		contentDiv.appendChild(docWrapper);
	});

	optDetailsDiv.appendChild(heading);
	optDetailsDiv.appendChild(contentDiv);

	return optDetailsDiv;
}
function generateEducationalDetails(personId) {
	const eduDetailsDiv = document.createElement('div');
	eduDetailsDiv.classList.add('h-fit', 'w-full', 'flex', 'flex-col', 'gap-2', 'bg-white', 'bg-opacity-50', 'rounded-xl');

	// Expandable heading
	const heading = document.createElement('div');
	heading.id = `expandable-${personId + 'edu'}`;
	heading.innerText = '+  Educational Details';
	heading.classList.add('w-full', 'text-3xl', 'font-bold', 'flex', 'gap-2', 'p-5', 'text-red-600', 'select-none', 'cursor-pointer');

	// Expandable content
	const contentDiv = document.createElement('div');
	contentDiv.id = `expandable-content-${personId + 'edu'}`;
	contentDiv.classList.add('hidden', 'p-5', 'space-y-5', 'flex-col', 'w-full', 'h-fit', 'font-medium');

	// Education form container
	const formContainer = document.createElement('div');
	formContainer.classList.add('flex', 'flex-col', 'gap-10', 'w-full');
	formContainer.id = `education-form-container-${personId}`;

	// ✅ Ensure formData structure exists

	if (!formData[personId].educational_details) {
		formData[personId].educational_details = [];
	}
	let additionIntoFetched = false;
	// Function to add an education form dynamically
	function addEducationForm(existingData = {}) {
		let eduFieldWrapper = document.createElement('div');
		eduFieldWrapper.classList.add(
			'education-form',
			'border-2',
			'border-white',
			'p-4',
			'rounded-xl',
			'bg-white',
			'bg-opacity-50',
			'flex',
			'flex-col',
			'gap-3'
		);
		// Form fields
		const fields = [
			{ label: 'Institute Name:', class: 'education-institute', type: 'text', key: 'institute', value: existingData.institute || '' },
			{ label: 'Field of Study:', class: 'education-field', type: 'text', key: 'field', value: existingData.field || '' },
			{ label: 'Address:', class: 'education-address', type: 'text', key: 'address', value: existingData.address || '' },
			{ label: 'Start Date:', class: 'education-start', type: 'date', key: 'start_date', value: existingData.start_date || '' },
			{ label: 'End Date:', class: 'education-end', type: 'date', key: 'end_date', value: existingData.end_date || '' },
		];
		// Create inputs
		fields.forEach((field) => {
			const label = document.createElement('label');
			label.classList.add('block', 'text-base');
			label.innerText = field.label;

			const input = document.createElement('input');
			input.type = field.type;
			input.classList.add(
				field.class,
				'w-full',
				'bg-white',
				'rounded-lg',
				'text-lg',
				'ring-1',
				'ring-red-300',
				'outline-none',
				'focus:ring-2',
				'px-3',
				'py-2'
			);
			input.placeholder = 'Type here...';
			input.value = field.value;

			// Add event listener to update formData
			input.addEventListener('input', function () {
				existingData[field.key] = input.value;
			});

			eduFieldWrapper.appendChild(label);
			eduFieldWrapper.appendChild(input);
		});
		// Remove button
		const removeButton = document.createElement('button');
		removeButton.type = 'button';
		removeButton.classList.add('remove-education', 'text-red-600', 'mt-3', 'text-lg');
		removeButton.innerText = 'Remove';
		// Hide remove button if user role is visa_agent
		if (userRole === 'visa_agent') {
			removeButton.style.display = 'none';
		}
		// Remove event listener
		removeButton.addEventListener('click', function () {
			formContainer.removeChild(eduFieldWrapper);
			formData[personId].educational_details = formData[personId].educational_details.filter((edu) => edu !== existingData);
		});

		eduFieldWrapper.appendChild(removeButton);
		formContainer.appendChild(eduFieldWrapper);

		// ✅ Add to formData
		if (!isFetchedDocument || additionIntoFetched) {
			formData[personId].educational_details.push(existingData);
		}
	}

	// Populate existing education details
	if (isFetchedDocument) {
		formData[personId].educational_details.forEach((edu) => addEducationForm(edu));
	}

	// Add More Education Button (Hidden for visa agents)
	const addMoreButton = document.createElement('button');
	addMoreButton.innerText = '+ Add More Educational Details';
	addMoreButton.type = 'button';
	addMoreButton.classList.add(
		'text-lg',
		'px-5',
		'py-2',
		'place-self-end',
		'bg-white',
		'text-red-600',
		'font-medium',
		'rounded-full',
		'flex',
		'items-center',
		'justify-center',
		'gap-10',
		'hover:bg-red-600',
		'hover:text-white',
		'cursor-pointer'
	);

	// Hide add button for visa_agent
	if (userRole === 'visa_agent') {
		addMoreButton.style.display = 'none';
	}

	addMoreButton.addEventListener('click', () => {
		additionIntoFetched = true;
		addEducationForm({});
	});

	contentDiv.appendChild(formContainer);
	contentDiv.appendChild(addMoreButton);
	eduDetailsDiv.appendChild(heading);
	eduDetailsDiv.appendChild(contentDiv);

	return eduDetailsDiv;
}
function generateSocialDetails(personId) {
	const socialDetailsDiv = document.createElement('div');
	socialDetailsDiv.classList.add('h-fit', 'w-full', 'flex', 'flex-col', 'gap-2', 'bg-white', 'bg-opacity-50', 'rounded-xl');

	// Expandable heading
	const heading = document.createElement('div');
	heading.id = `expandable-${personId + 'social'}`;
	heading.innerText = '+ Social Details';
	heading.classList.add('w-full', 'text-3xl', 'font-bold', 'flex', 'gap-2', 'p-5', 'text-red-600', 'select-none', 'cursor-pointer');

	// Expandable content
	const contentDiv = document.createElement('div');
	contentDiv.id = `expandable-content-${personId + 'social'}`;
	contentDiv.classList.add('hidden', 'p-5', 'space-y-5', 'flex-col', 'w-full', 'h-fit', 'font-medium');

	// Form container
	const formContainer = document.createElement('div');
	formContainer.classList.add('flex', 'flex-col', 'gap-6', 'w-full');
	formContainer.id = `social-form-container-${personId}`;

	// Ensure formData structure exists
	if (!formData[personId].social_details || formData[personId].social_details?.length <= 0) {
		formData[personId].social_details = {};
	}

	// Define form fields
	const fields = [
		{ label: 'E-Mail Address', type: 'email', key: 'email' },
		{ label: 'Another E-Mail Address', type: 'email', key: 'alt_email' },
		{ label: 'Contact Number', type: 'tel', key: 'contact' },
		{ label: 'Another Contact Number', type: 'tel', key: 'alt_contact' },
		{ label: 'Social Media', type: 'text', key: 'social_media' },
	];

	// Create inputs dynamically
	fields.forEach((field) => {
		const wrapper = document.createElement('div');
		wrapper.classList.add('mb-4');

		const label = document.createElement('label');
		label.classList.add('block', 'text-sm', 'font-semibold');
		label.innerText = field.label;

		const input = document.createElement('input');
		input.type = field.type;
		input.classList.add(
			'w-full',
			'sm:w-1/2',
			'border-b-2',
			'placeholder:text-gray-600',
			'bg-transparent',
			'text-lg',
			'border-black',
			'placeholder:text-sm',
			'outline-none',
			'p-2'
		);
		input.placeholder = 'Type here...';
		input.value = formData[personId].social_details?.[field.key] || '';

		// Event listener to update formData
		input.addEventListener('input', () => {
			formData[personId].social_details[field.key] = input.value.trim();
			console.log(formData);
		});

		wrapper.appendChild(label);
		wrapper.appendChild(input);
		formContainer.appendChild(wrapper);
	});

	contentDiv.appendChild(formContainer);
	socialDetailsDiv.appendChild(heading);
	socialDetailsDiv.appendChild(contentDiv);

	return socialDetailsDiv;
}
function generateParentsInfo(personId) {
	const parentsInfoDiv = document.createElement('div');
	parentsInfoDiv.classList.add('h-fit', 'w-full', 'flex', 'flex-col', 'gap-2', 'bg-white', 'bg-opacity-50', 'rounded-xl');

	// Expandable heading
	const heading = document.createElement('div');
	heading.id = `expandable-${personId + 'parents'}`;
	heading.innerText = '+ Parents Date of Birth / Date of Death';
	heading.classList.add('w-full', 'text-3xl', 'font-bold', 'flex', 'gap-2', 'p-5', 'text-red-600', 'select-none', 'cursor-pointer');

	// Expandable content
	const contentDiv = document.createElement('div');
	contentDiv.id = `expandable-content-${personId + 'parents'}`;
	contentDiv.classList.add('hidden', 'p-5', 'gap-5', 'flex-col', 'w-full', 'h-fit', 'font-medium');

	// Form container
	const formContainer = document.createElement('div');
	formContainer.classList.add('flex', 'flex-col', 'gap-5', 'w-full');
	formContainer.id = `parents-form-container-${personId}`;

	// Ensure formData structure exists

	if (!formData[personId].parents_info || formData[personId].parents_info.length <= 0) {
		formData[personId].parents_info = {};
	}

	// Define form fields
	const fields = [
		{ label: 'Father Date of Birth', type: 'date', key: 'father_dob' },
		{ label: 'Mother Date of Birth', type: 'date', key: 'mother_dob' },
		{ label: 'Father Date of Death', type: 'date', key: 'father_dod' },
		{ label: 'Mother Date of Death', type: 'date', key: 'mother_dod' },
	];

	// Create inputs dynamically
	fields.forEach((field) => {
		const wrapper = document.createElement('div');
		wrapper.classList.add('flex', 'w-full', 'items-center', 'justify-between');

		const label = document.createElement('p');
		label.classList.add('text-xl');
		label.innerText = field.label;

		const input = document.createElement('input');
		input.type = field.type;
		input.classList.add('bg-white', 'rounded-lg', 'px-2.5', 'text-lg');
		input.value = formData[personId].parents_info?.[field.key] || '';

		// Event listener to update formData
		input.addEventListener('input', () => {
			formData[personId].parents_info[field.key] = input.value.trim();
		});

		wrapper.appendChild(label);
		wrapper.appendChild(input);
		formContainer.appendChild(wrapper);
	});

	contentDiv.appendChild(formContainer);
	parentsInfoDiv.appendChild(heading);
	parentsInfoDiv.appendChild(contentDiv);

	return parentsInfoDiv;
}
function generateRefusalInfo(personId) {
	const refusalInfoDiv = document.createElement('div');
	refusalInfoDiv.classList.add('h-fit', 'w-full', 'flex', 'flex-col', 'gap-2', 'bg-white', 'bg-opacity-50', 'rounded-xl');

	// Expandable heading
	const heading = document.createElement('div');
	heading.id = `expandable-${personId + 'refusal'}`;
	heading.innerText = '+ Any Refusal';
	heading.classList.add('w-full', 'text-3xl', 'font-bold', 'flex', 'gap-2', 'p-5', 'text-red-600', 'select-none', 'cursor-pointer');

	// Expandable content
	const contentDiv = document.createElement('div');
	contentDiv.id = `expandable-content-${personId + 'refusal'}`;
	contentDiv.classList.add('hidden', 'p-5', 'space-y-5', 'flex-col', 'w-full', 'h-fit', 'font-medium');

	// Form container
	const formContainer = document.createElement('div');
	formContainer.classList.add('flex', 'flex-col', 'gap-5', 'w-full');
	formContainer.id = `refusal-form-container-${personId}`;

	// Ensure formData structure exists
	if (!formData[personId].refusal_info || formData[personId].refusal_info.length <= 0) {
		formData[personId].refusal_info = {};
	}

	// Define form fields
	const fields = [
		{ label: 'Country Name', type: 'text', key: 'refusal_country', placeholder: 'Type here...' },
		{ label: 'Refusal Date', type: 'date', key: 'refusal_date' },
	];

	// Create inputs dynamically
	fields.forEach((field) => {
		const wrapper = document.createElement('div');
		wrapper.classList.add('flex', 'w-full', 'sm:w-2/3', 'items-center', 'justify-between');

		const label = document.createElement('p');
		label.classList.add('text-xl');
		label.innerText = field.label;

		const input = document.createElement('input');
		input.type = field.type;
		input.classList.add('bg-white', 'rounded-lg', 'px-2.5', 'text-lg', 'outline-none');
		input.placeholder = field.placeholder || '';
		input.value = formData[personId].refusal_info?.[field.key] || '';

		// Event listener to update formData
		input.addEventListener('input', () => {
			formData[personId].refusal_info[field.key] = input.value.trim();
		});

		wrapper.appendChild(label);
		wrapper.appendChild(input);
		formContainer.appendChild(wrapper);
	});

	contentDiv.appendChild(formContainer);
	refusalInfoDiv.appendChild(heading);
	refusalInfoDiv.appendChild(contentDiv);

	return refusalInfoDiv;
}
function generateOtherInfo(personId) {
	const otherInfoDiv = document.createElement('div');
	otherInfoDiv.classList.add('h-fit', 'w-full', 'flex', 'flex-col', 'gap-2', 'bg-white', 'bg-opacity-50', 'rounded-xl');

	// Expandable heading
	const heading = document.createElement('div');
	heading.id = `expandable-${personId + 'other'}`;
	heading.innerText = '+ Other Information';
	heading.classList.add('w-full', 'text-3xl', 'font-bold', 'flex', 'gap-2', 'p-5', 'text-red-600', 'select-none', 'cursor-pointer');

	// Expandable content
	const contentDiv = document.createElement('div');
	contentDiv.id = `expandable-content-${personId + 'other'}`;
	contentDiv.classList.add('hidden', 'p-5', 'space-y-5', 'flex-col', 'w-full', 'h-fit', 'font-medium');

	// Form container
	const formContainer = document.createElement('div');
	formContainer.classList.add('flex', 'flex-col', 'gap-5', 'w-full');
	formContainer.id = `other-form-container-${personId}`;

	// Ensure formData structure exists

	if (!formData[personId].other_info || formData[personId].other_info <= 0) {
		formData[personId].other_info = {};
	}

	// Define form fields
	const fields = [
		{
			label: 'Who owns the property?',
			type: 'select',
			key: 'property_owner',
			options: [
				{ value: '', text: 'Options' },
				{ value: 'me', text: 'Me' },
				{ value: 'parents', text: 'Parents' },
				{ value: 'other', text: 'Other' },
			],
		},
		{ label: 'How long have you been living at this address?', type: 'number', key: 'living_duration', placeholder: 'Living Duration' },
		{
			label: 'Previously applied for the same country?',
			type: 'select',
			key: 'previously_applied',
			options: [
				{ value: 'yes', text: 'Yes' },
				{ value: 'no', text: 'No' },
			],
		},
	];

	// Create inputs dynamically
	fields.forEach((field) => {
		const wrapper = document.createElement('div');
		wrapper.classList.add('flex', 'w-full', 'justify-between');

		const label = document.createElement('p');
		label.classList.add('text-xl');
		label.innerText = field.label;

		let input;
		if (field.type === 'select') {
			input = document.createElement('select');
			field.options.forEach((option) => {
				const opt = document.createElement('option');
				opt.value = option.value;
				opt.innerText = option.text;
				input.appendChild(opt);
			});
		} else {
			input = document.createElement('input');
			input.type = field.type;
			input.placeholder = field.placeholder || '';
		}

		input.classList.add('w-32', 'py-1', 'px-2', 'text-base', 'border-2', 'border-red-300', 'text-black', 'focus:outline-none', 'rounded-lg');
		input.value = formData[personId].other_info?.[field.key] || '';

		// Event listener to update formData
		input.addEventListener('input', () => {
			formData[personId].other_info[field.key] = input.value.trim();
		});

		wrapper.appendChild(label);
		wrapper.appendChild(input);
		formContainer.appendChild(wrapper);
	});

	// Parents Residential Address
	const addressWrapper = document.createElement('div');
	addressWrapper.classList.add('flex', 'flex-col', 'gap-4', 'border-red-300');

	const addressLabel = document.createElement('p');
	addressLabel.classList.add('font-semibold', 'text-3xl', 'mt-6');
	addressLabel.innerText = 'Parents Residential Address';

	const otherAddressLabel = document.createElement('label');
	otherAddressLabel.setAttribute('for', `parents_address-${personId}`);
	otherAddressLabel.classList.add('text-lg');

	const otherAddressInput = document.createElement('input');
	otherAddressInput.type = 'text';
	otherAddressInput.id = `parents_address-${personId}`;
	otherAddressInput.classList.add(
		'w-full',
		'text-base',
		'truncate',
		'overflow-x-scroll',
		'placeholder:text-gray-600',
		'py-1',
		'bg-transparent',
		'border-b-2',
		'border-black',
		'outline-none',
		'px-2'
	);
	otherAddressInput.placeholder = 'Type here...';
	otherAddressInput.value = formData[personId].other_info.parents_address || '';

	otherAddressInput.addEventListener('input', () => {
		formData[personId].other_info.parents_address = otherAddressInput.value.trim();
	});

	addressWrapper.appendChild(addressLabel);
	addressWrapper.appendChild(otherAddressLabel);
	addressWrapper.appendChild(otherAddressInput);
	formContainer.appendChild(addressWrapper);

	contentDiv.appendChild(formContainer);
	otherInfoDiv.appendChild(heading);
	otherInfoDiv.appendChild(contentDiv);

	return otherInfoDiv;
}
function generateThreeMonthTripDetails(personId) {
	const tripDetailsDiv = document.createElement('div');
	tripDetailsDiv.classList.add('h-fit', 'w-full', 'flex', 'flex-col', 'gap-2', 'bg-white', 'bg-opacity-50', 'rounded-xl');

	// Expandable heading
	const heading = document.createElement('div');
	heading.id = `expandable-${personId + '3trip'}`;
	heading.innerText = '+  Three-Month Trip History';
	heading.classList.add('w-full', 'text-3xl', 'font-bold', 'flex', 'gap-2', 'p-5', 'text-red-600', 'select-none', 'cursor-pointer');

	// Expandable content
	const contentDiv = document.createElement('div');
	contentDiv.id = `expandable-content-${personId + '3trip'}`;
	contentDiv.classList.add('hidden', 'p-5', 'space-y-5', 'flex-col', 'w-full', 'h-fit', 'font-medium');

	// Trip form container
	const formContainer = document.createElement('div');
	formContainer.classList.add('flex', 'flex-col', 'gap-10', 'w-full');
	formContainer.id = `trip-form-container-${personId}`;

	// Ensure formData structure exists

	if (!formData[personId].three_months_history) {
		formData[personId].three_months_history = [];
	}
	let additionIntoFetched = false;
	// Function to add a trip entry dynamically
	function addTripEntry(existingData = {}) {
		if (!formData[personId].three_months_history.includes(existingData)) {
			formData[personId].three_months_history.push(existingData);
		}
		let tripFieldWrapper = document.createElement('div');
		tripFieldWrapper.classList.add(
			'trip-form',
			'border-2',
			'border-white',
			'p-4',
			'rounded-xl',
			'bg-white',
			'bg-opacity-50',
			'flex',
			'flex-col',
			'gap-3'
		);

		// Form fields
		const fields = [
			{ label: 'Country:', class: 'trip-country', type: 'text', key: 'country', value: existingData.country || '' },
			{ label: 'Start Date:', class: 'trip-start', type: 'date', key: 'start_date', value: existingData.start_date || '' },
			{ label: 'End Date:', class: 'trip-end', type: 'date', key: 'end_date', value: existingData.end_date || '' },
		];

		// Create inputs
		fields.forEach((field) => {
			const label = document.createElement('label');
			label.classList.add('block', 'text-base');
			label.innerText = field.label;

			const input = document.createElement('input');
			input.type = field.type;
			input.classList.add(
				field.class,
				'w-full',
				'bg-white',
				'rounded-lg',
				'text-lg',
				'ring-1',
				'ring-red-300',
				'outline-none',
				'focus:ring-2',
				'px-3',
				'py-2'
			);
			input.placeholder = 'Type here...';
			input.value = field.value;

			// Add event listener to update formData
			input.addEventListener('input', function () {
				existingData[field.key] = input.value;
			});

			tripFieldWrapper.appendChild(label);
			tripFieldWrapper.appendChild(input);
		});

		// Remove button
		const removeButton = document.createElement('button');
		removeButton.type = 'button';
		removeButton.classList.add('remove-trip', 'text-red-600', 'mt-3', 'text-lg');
		removeButton.innerText = 'Remove';

		// Hide remove button if user role is visa_agent
		if (userRole === 'visa_agent') {
			removeButton.style.display = 'none';
		}

		// Remove event listener
		removeButton.addEventListener('click', function () {
			formContainer.removeChild(tripFieldWrapper);
			formData[personId].three_months_history = formData[personId].three_months_history.filter((trip) => trip !== existingData);
		});

		tripFieldWrapper.appendChild(removeButton);
		formContainer.appendChild(tripFieldWrapper);

		// Add to formData
		if (!isFetchedDocument || additionIntoFetched) {
			formData[personId].three_months_history.push(existingData);
		}
	}

	if (isFetchedDocument) {
		formData[personId].three_months_history.forEach((trip) => addTripEntry(trip));
	}

	// Add More Trip Button (Hidden for visa agents)
	const addMoreButton = document.createElement('button');
	addMoreButton.innerText = '+ Add 3 Month Trip History';
	addMoreButton.type = 'button';
	addMoreButton.classList.add(
		'text-lg',
		'px-5',
		'py-2',
		'place-self-end',
		'bg-white',
		'text-red-600',
		'font-medium',
		'rounded-full',
		'flex',
		'items-center',
		'justify-center',
		'gap-10',
		'hover:bg-red-600',
		'hover:text-white',
		'cursor-pointer'
	);

	// Hide add button for visa_agent
	if (userRole === 'visa_agent') {
		addMoreButton.style.display = 'none';
	}

	addMoreButton.addEventListener('click', () => {
		additionIntoFetched = true;
		addTripEntry({});
	});

	contentDiv.appendChild(formContainer);
	contentDiv.appendChild(addMoreButton);
	tripDetailsDiv.appendChild(heading);
	tripDetailsDiv.appendChild(contentDiv);

	return tripDetailsDiv;
}
function generateSixMonthTripDetails(personId) {
	const tripDetailsDiv = document.createElement('div');
	tripDetailsDiv.classList.add('h-fit', 'w-full', 'flex', 'flex-col', 'gap-2', 'bg-white', 'bg-opacity-50', 'rounded-xl');

	// Expandable heading
	const heading = document.createElement('div');
	heading.id = `expandable-${personId + '6trip'}`;
	heading.innerText = '+  Six-Month Trip History';
	heading.classList.add('w-full', 'text-3xl', 'font-bold', 'flex', 'gap-2', 'p-5', 'text-red-600', 'select-none', 'cursor-pointer');

	// Expandable content
	const contentDiv = document.createElement('div');
	contentDiv.id = `expandable-content-${personId + '6trip'}`;
	contentDiv.classList.add('hidden', 'p-5', 'space-y-5', 'flex-col', 'w-full', 'h-fit', 'font-medium');

	// Trip form container
	const formContainer = document.createElement('div');
	formContainer.classList.add('flex', 'flex-col', 'gap-10', 'w-full');
	formContainer.id = `trip-form-container-${personId}`;

	// Ensure formData structure exists

	if (!formData[personId].six_months_history) {
		formData[personId].six_months_history = [];
	}
	let additionIntoFetched = false;
	// Function to add a trip form dynamically
	function addTripForm(existingData = {}) {
		let tripFieldWrapper = document.createElement('div');
		tripFieldWrapper.classList.add(
			'trip-form',
			'border-2',
			'border-white',
			'p-4',
			'rounded-xl',
			'bg-white',
			'bg-opacity-50',
			'flex',
			'flex-col',
			'gap-3'
		);

		// Form fields
		const fields = [
			{ label: 'Country:', class: 'trip-country', type: 'text', key: 'country', value: existingData.country || '' },
			{ label: 'Start Date:', class: 'trip-start', type: 'date', key: 'start_date', value: existingData.start_date || '' },
			{ label: 'End Date:', class: 'trip-end', type: 'date', key: 'end_date', value: existingData.end_date || '' },
		];

		// Create inputs
		fields.forEach((field) => {
			const label = document.createElement('label');
			label.classList.add('block', 'text-base');
			label.innerText = field.label;

			const input = document.createElement('input');
			input.type = field.type;
			input.classList.add(
				field.class,
				'w-full',
				'bg-white',
				'rounded-lg',
				'text-lg',
				'ring-1',
				'ring-red-300',
				'outline-none',
				'focus:ring-2',
				'px-3',
				'py-2'
			);
			input.placeholder = 'Type here...';
			input.value = field.value;

			// Add event listener to update formData
			input.addEventListener('input', function () {
				existingData[field.key] = input.value;
			});

			tripFieldWrapper.appendChild(label);
			tripFieldWrapper.appendChild(input);
		});

		// Remove button
		const removeButton = document.createElement('button');
		removeButton.type = 'button';
		removeButton.classList.add('remove-trip', 'text-red-600', 'mt-3', 'text-lg');
		removeButton.innerText = 'Remove';

		// Hide remove button if user role is visa_agent
		if (userRole === 'visa_agent') {
			removeButton.style.display = 'none';
		}

		// Remove event listener
		removeButton.addEventListener('click', function () {
			formContainer.removeChild(tripFieldWrapper);
			formData[personId].six_months_history = formData[personId].six_months_history.filter((trip) => trip !== existingData);
		});

		tripFieldWrapper.appendChild(removeButton);
		formContainer.appendChild(tripFieldWrapper);

		// Add to formData
		if (!isFetchedDocument || additionIntoFetched) {
			formData[personId].six_months_history.push(existingData);
		}
	}

	// Populate existing trip details
	if (isFetchedDocument) {
		formData[personId].six_months_history.forEach((trip) => addTripForm(trip));
	}

	// Add More Trips Button (Hidden for visa agents)
	const addMoreButton = document.createElement('button');
	addMoreButton.innerText = '+ Add 6 Month Trip History';
	addMoreButton.type = 'button';
	addMoreButton.classList.add(
		'text-lg',
		'px-5',
		'py-2',
		'place-self-end',
		'bg-white',
		'text-red-600',
		'font-medium',
		'rounded-full',
		'flex',
		'items-center',
		'justify-center',
		'gap-10',
		'hover:bg-red-600',
		'hover:text-white',
		'cursor-pointer'
	);

	// Hide add button for visa_agent
	if (userRole === 'visa_agent') {
		addMoreButton.style.display = 'none';
	}

	addMoreButton.addEventListener('click', () => {
		additionIntoFetched = true;
		addTripForm({});
	});

	contentDiv.appendChild(formContainer);
	contentDiv.appendChild(addMoreButton);
	tripDetailsDiv.appendChild(heading);
	tripDetailsDiv.appendChild(contentDiv);

	return tripDetailsDiv;
}
function createEmploymentHistoryForm(personId) {
	const employmentDetailsDiv = document.createElement('div');
	employmentDetailsDiv.classList.add('h-fit', 'w-full', 'flex', 'flex-col', 'gap-2', 'bg-white', 'bg-opacity-50', 'rounded-xl');

	// Expandable heading
	const heading = document.createElement('div');
	heading.id = `expandable-${personId + 'employment'}`;
	heading.innerText = '+  Employment History';
	heading.classList.add('w-full', 'text-3xl', 'font-bold', 'flex', 'gap-2', 'p-5', 'text-red-600', 'select-none', 'cursor-pointer');

	// Expandable content
	const contentDiv = document.createElement('div');
	contentDiv.id = `expandable-content-${personId + 'employment'}`;
	contentDiv.classList.add('hidden', 'p-5', 'space-y-5', 'items-end', 'flex-col', 'w-full', 'h-fit', 'font-medium');

	// Employment form container
	const formContainer = document.createElement('div');
	formContainer.classList.add('flex', 'flex-col', 'gap-10', 'w-full');
	formContainer.id = `employment-form-container-${personId}`;

	// Ensure formData structure exists

	if (!formData[personId].employment_history) {
		formData[personId].employment_history = [];
	}
	additionIntoFetched = false;
	// Function to add an employment entry dynamically
	function addEmploymentEntry(existingData = {}) {
		let employmentFieldWrapper = document.createElement('div');
		employmentFieldWrapper.classList.add(
			'employment-form',
			'border-2',
			'border-white',
			'p-4',
			'rounded-xl',
			'bg-white',
			'bg-opacity-50',
			'flex',
			'flex-col',
			'gap-3'
		);

		// Form fields
		const fields = [
			{ label: 'Company Name:', class: 'employment-company', type: 'text', key: 'company_name', value: existingData.company_name || '' },
			{ label: 'Designation:', class: 'employment-designation', type: 'text', key: 'designation', value: existingData.designation || '' },
			{ label: 'Supervisor Name:', class: 'employment-supervisor', type: 'text', key: 'supervisor_name', value: existingData.supervisor_name || '' },
			{ label: 'Start Date:', class: 'employment-start', type: 'date', key: 'start_date', value: existingData.start_date || '' },
			{ label: 'End Date:', class: 'employment-end', type: 'date', key: 'end_date', value: existingData.end_date || '' },
			{ label: 'Company Address:', class: 'employment-address', type: 'text', key: 'company_address', value: existingData.company_address || '' },
		];

		// Create inputs
		fields.forEach((field) => {
			const label = document.createElement('label');
			label.classList.add('block', 'text-base');
			label.innerText = field.label;

			const input = document.createElement('input');
			input.type = field.type;
			input.classList.add(
				field.class,
				'w-full',
				'bg-white',
				'rounded-lg',
				'text-lg',
				'ring-1',
				'ring-red-300',
				'outline-none',
				'focus:ring-2',
				'px-3',
				'py-2'
			);
			input.placeholder = 'Type here...';
			input.value = field.value;

			// Add event listener to update formData
			input.addEventListener('input', function () {
				existingData[field.key] = input.value;
			});

			employmentFieldWrapper.appendChild(label);
			employmentFieldWrapper.appendChild(input);
		});

		// Remove button
		const removeButton = document.createElement('button');
		removeButton.type = 'button';
		removeButton.classList.add('remove-employment', 'text-red-600', 'mt-3', 'text-lg');
		removeButton.innerText = 'Remove';

		// Hide remove button if user role is visa_agent
		if (userRole === 'visa_agent') {
			removeButton.style.display = 'none';
		}

		// Remove event listener
		removeButton.addEventListener('click', function () {
			formContainer.removeChild(employmentFieldWrapper);
			formData[personId].employment_history = formData[personId].employment_history.filter((employment) => employment !== existingData);
		});

		employmentFieldWrapper.appendChild(removeButton);
		formContainer.appendChild(employmentFieldWrapper);

		// Add to formData
		if (!isFetchedDocument || additionIntoFetched) {
			formData[personId].employment_history.push(existingData);
		}
	}

	// Populate existing employment history
	if (isFetchedDocument) {
		formData[personId].employment_history.forEach((employment) => addEmploymentEntry(employment));
	}

	// Add More Employment Button (Hidden for visa agents)
	const addMoreButton = document.createElement('button');
	addMoreButton.innerText = '+ Add More Employment History';
	addMoreButton.type = 'button';
	addMoreButton.classList.add(
		'px-5',
		'py-2',
		'bg-white',
		'text-red-600',
		'font-medium',
		'rounded-full',
		'hover:bg-red-600',
		'hover:text-white',
		'cursor-pointer',
		'w-fit'
	);

	// Hide add button for visa_agent
	if (userRole === 'visa_agent') {
		addMoreButton.style.display = 'none';
	}

	addMoreButton.addEventListener('click', () => {
		additionIntoFetched = true;
		addEmploymentEntry({});
	});

	contentDiv.appendChild(formContainer);
	contentDiv.appendChild(addMoreButton);
	employmentDetailsDiv.appendChild(heading);
	employmentDetailsDiv.appendChild(contentDiv);

	return employmentDetailsDiv;
}
//
//
//
//
//
function bindExpandableListeners() {
	$(document).off('click', '[id^=expandable-]'); // Remove any previous bindings
	$(document).on('click', '[id^=expandable-]', function () {
		const idPrefix = this.id.split('-')[1];
		$(`#expandable-content-${idPrefix}`).toggleClass('hidden');
		$(`#expandable-content-${idPrefix}`).toggleClass('flex');
	});
}
var arrayFormaingData = [
	{ id: 'taveling_start', column: 'traveling_plan', container_id: '', type: 'input' },
	{ id: 'taveling_end', column: 'traveling_plan', container_id: '', type: 'input' },
	{ id: 'special_request', column: 'traveling_plan', container_id: '', type: 'input' },
];
function initializeArrayToFormData() {
	arrayFormaingData.forEach(({ id, column, container_id, type }) => {
		if (formData[column] && typeof formData[column] === 'string') {
			try {
				formData[column] = JSON.parse(formData[column]);
			} catch {
				formData[column] = [];
			}
		}
		formData[column] = Array.isArray(formData[column]) ? formData[column] : [];

		const columnData = formData[column];
		const container = document.getElementById(container_id);
		const element = document.getElementById(id);
		if (!element) return;

		const updateColumnData = (value) => {
			let existingValue = columnData.find((data) => data.id === id);
			existingValue ? (existingValue.value = value) : columnData.push({ id, value });
		};

		if (isFetchedDocument) {
			const existingValue = columnData.find((data) => data.id === id);
			if (existingValue?.value) {
				element.value = existingValue.value;
				container?.classList.replace('hidden', 'flex');
			}
		}

		if (type === 'checkbox') {
			element.checked = columnData.some((data) => data.id === id && data.checked);
			element.addEventListener('change', (event) => updateColumnData(event.target.checked));
		} else if (type === 'input' || type === 'select') {
			element.addEventListener(type === 'input' ? 'input' : 'change', (event) => updateColumnData(event.target.value));
		}
	});
	setTimeout(function(){
		let el = document.getElementById('application_city');

		if (el && el.value) {
			formData['application_city'] = el.value;
		}
	},1000)
}

//
//
//
//
// save or submit
$(document).on('click', '#save_form', async function (event) {
	event.preventDefault(); // Prevent default form submission

	if (userRole !== 'visa_agent') {
		// if (userRole === 'data_entry_agent') {
		// 	formData['status'] = 'In Process';
		// }
		// Prevent duplicate submission
		if (!$(this).data('submitted')) {
			$(this).data('submitted', true); // Mark as submitted
			await submitForm(); // Ensure async execution
			$(this).data('submitted', false); // Reset flag after submission
		}
	} else {
		alert('You do not have permission to perform this action.');
	}
});
async function submitForm() {
	const requiredFields = [
		'applicant_name',
		'applicant_surname',
		'applicant_cnic',
		'application_city',
		'application_country_id',
		'data_entry_agent',
		'visa_agent',
		'passport_number',
		'persons',
		'phone_number',
		'total_amount',
		'deadline_date'
	];

	const missingFields = requiredFields.filter((field) => !formData[field]);
	if (missingFields.length > 0) {
		alert(`Please fill in all required fields: ${missingFields.join(', ')}`);
		return;
	}

	const method = formData.id !== null ? 'PUT' : 'POST';
	const url = '../backend/application_controller.php';
	// Submit the form data

	if (formData.advance_amount && formData.total_amount) {
		// Ensure that both values are numeric and check the condition
		const advanceAmount = parseFloat(formData.advance_amount);
		const totalAmount = parseFloat(formData.total_amount);

		if (isNaN(advanceAmount) || isNaN(totalAmount)) {
			alert('Invalid input. Please enter valid numbers.');
			return;
		}

		if (advanceAmount > totalAmount) {
			alert('Advance amount must be less than or equal to total amount.');
			return;
		}
	}

    let isChecked = document.getElementById('proceed_to_agent').checked;

    if (isChecked) {
      formData.proceed_to_agent = true;
    }

	try {
		const response = await fetch(url, {
			method: method,
			headers: {
				'Content-Type': 'application/json',
			},
			credentials: 'include', // Ensures session cookies are sent
			body: JSON.stringify(formData),
		});


		if (!response.ok) {
			throw new Error(`HTTP error! Status: ${response.status}`);
		}

		const data = await response.json();
		if (data.id) {
			formData.id = data.id; // Update formData with the returned ID
		}

		alert(data.message || 'Operation successful!');
		loadComponent('applications.php', 'Applications');
	} catch (error) {
		console.error('Fetch Error:', error);
		alert('An error occurred. Please try again.');
	}
}

function restrictEditing(extraData) {
	const fieldIds = [
		'applicant_name',
		'applicant_surname',
		'applicant_cnic',
		'application_city',
		'application_country_id',
		'data_entry_agent',
		'visa_agent',
		'passport_number',
		'persons',
		'phone_number',
		'total_amount',
		'occupation',
		'applicant_address',
		// 'application_limit',
		'deadline_date',
	];
	if (userRole === 'visa_agent' || userRole === 'sales_agent') {
		setTimeout(() => {
			document.querySelectorAll('input, select, textarea').forEach((element) => {
				element.setAttribute('disabled', 'true');
			});
		}, 150);
	}

	if (extraData && extraData.id  && userRole === 'data_entry_agent') {
		const fieldIds2 = [
			'application_city',
			'application_country_id',
			'data_entry_agent',
			'visa_agent',
			'persons',
			'total_amount',
			'occupation',
		];
		fieldIds2.forEach((id) => {
			const element = document.getElementById(id);
			if (element) {
				element.setAttribute('disabled', 'true');
			} else {
				console.log(`Element with ID ${id} not found.`);
			}
		});
	}


	if (extraData && extraData.id && userRole === 'manager') {
		const element = document.getElementById('total_amount');
		if (element) {
			element.setAttribute('disabled', 'true');
		} else {
			console.log(`Element with ID ${id} not found.`);
		}
	}
	// if (userRole === 'data_entry_agent') {
	// 	fieldIds.forEach((id) => {
	// 		document.getElementById(id).setAttribute('disabled', 'true');
	// 	});
	// }
}
