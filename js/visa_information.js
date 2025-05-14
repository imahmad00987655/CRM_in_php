// Fetch and populate countries in the dropdown
document.addEventListener('DOMContentLoaded', () => {
	const countryDropdown = document.getElementById('country');
	const documentsContainer = document.getElementById('documents-container');
	const documentList = document.getElementById('document-list');
	const isAdmin = window.currentUser?.user_role == 'admin';
	// Hide admin-only elements if user is not an admin

	fetch('/backend/visainformationportal.php?action=get_countries')
		.then((response) => response.json())
		.then((countries) => {
			countries.forEach((country) => {
				const option = document.createElement('option');
				option.value = country.id;
				option.textContent = country.name;
				countryDropdown.appendChild(option);
			});
		})
		.catch((error) => console.error('Error fetching countries:', error));

	// Handle country selection
	countryDropdown.addEventListener('change', async (event) => {
		const selectedCountryId = event.target.value;

		if (!selectedCountryId) {
			documentsContainer.classList.add('hidden'); // Hide documents container
			return;
		}

		try {
			const response = await fetch(`/backend/visainformationportal.php?action=get_documents&parent_id=${selectedCountryId}`);
			const documents = await response.json();

			// Clear previous documents
			documentList.innerHTML = '';

			// Populate documents list
			documents.forEach((doc) => {
				const li = document.createElement('li');
				li.id = `doc-${doc.id}`;
				li.innerHTML = `
                <div class="flex flex-col sm:flex-row w-full sm:items-center gap-2">
                    <div class="w-full">
                        ${doc.document}
                    </div>
					<div class="w-fit flex sm:flex-col gap-2">
                 ${
										isAdmin
											? `
                    <button class="px-2 py-0.5 bg-blue-500 text-white rounded-lg hover:bg-blue-600 update-btn" data-id="${doc.id}">Update</button>
                    <button class="px-2 py-0.5 bg-red-500 text-white rounded-lg hover:bg-red-600 delete-btn" data-id="${doc.id}">Delete</button>`
											: ''
									}
									<div>
                </div>
            `;
				documentList.appendChild(li);
			});

			// Show documents container
			documentsContainer.classList.remove('hidden');

			// Enable Export PDF and Print buttons
			enableExportAndPrint(documents);
		} catch (error) {
			console.error('Error fetching documents:', error);
		}
	});

	// Event delegation for Update and Delete buttons
	documentList.addEventListener('click', (event) => {
		if (event.target.classList.contains('update-btn')) {
			handleUpdate(event);
		}
		if (event.target.classList.contains('delete-btn')) {
			handleDelete(event);
		}
	});

	document.getElementById('create-document').addEventListener('click', function () {
		createDocument();
	});

	// Handle Update Action
	async function createDocument() {
		const selectedCountryId = document.getElementById('country').value; // Ensure countryDropdown exists
		if (!selectedCountryId) {
			alert('Please select a country first.');
			return;
		}

		const newDocumentName = prompt('Enter the new document name:');
		if (!newDocumentName) return; // Exit if user cancels

		try {
			const response = await fetch('/backend/visainformationportal.php?action=create_document', {
				method: 'POST',
				headers: { 'Content-Type': 'application/json' },
				body: JSON.stringify({ parent_id: selectedCountryId, document: newDocumentName }),
			});

			const result = await response.json();
			if (result.success) {
				alert('Document created successfully!');
				location.reload(); // Refresh to update the document list
			} else {
				alert('Failed to create document: ' + (result.error || 'Unknown error.'));
			}
		} catch (error) {
			console.error('Error creating document:', error);
			alert('An error occurred while creating the document.');
		}
	}
	// Handle Update Action
	function handleUpdate(event) {
		const docId = event.target.dataset.id;
		const newDocumentName = prompt('Enter the updated document name:');
		if (newDocumentName) {
			fetch(`/backend/visainformationportal.php?action=update_document`, {
				method: 'POST',
				headers: { 'Content-Type': 'application/json' },
				body: JSON.stringify({ id: docId, document: newDocumentName }),
			})
				.then((response) => response.json())
				.then((result) => {
					if (result.success) {
						alert('Document updated successfully!');
						location.reload(); // Refresh the page to reflect changes
					} else {
						alert('Failed to update document.');
					}
				})
				.catch((error) => console.error('Error updating document:', error));
		}
	}
	// Handle Delete Action
	function handleDelete(event) {
		const docId = event.target.dataset.id;

		if (!docId) {
			console.error('Document ID is missing.');
			return;
		}

		if (confirm('Are you sure you want to delete this document?')) {
			fetch(`/backend/visainformationportal.php?action=delete_document`, {
				method: 'DELETE',
				headers: { 'Content-Type': 'application/json' },
				body: JSON.stringify({ id: docId }),
			})
				.then((response) => response.json())
				.then((result) => {
					if (result.success) {
						alert('Document deleted successfully!');
						location.reload(); // Refresh the page to reflect changes
					} else {
						alert('Failed to delete document.');
					}
				})
				.catch((error) => {
					console.error('Error deleting document:', error);
					alert('An error occurred while deleting the document.');
				});
		}
	}

	// Enable Export PDF and Print Buttons
	function enableExportAndPrint(documents) {
		const exportPdfButton = document.getElementById('export-pdf');
		const printButton = document.getElementById('print-documents');

		// Export PDF
		exportPdfButton.addEventListener('click', () => {
			const { jsPDF } = window.jspdf;
			const doc = new jsPDF();
			const countryName = countryDropdown.options[countryDropdown.selectedIndex].text;

			doc.text(`Documents for ${countryName}`, 10, 10);
			let yPos = 20;
			documents.forEach((docItem, index) => {
				doc.text(`${index + 1}. ${docItem.document}`, 10, yPos);
				yPos += 10;
			});

			doc.save(`${countryName}_documents.pdf`);
		});

		// Print Documents
		printButton.addEventListener('click', () => {
			const countryName = countryDropdown.options[countryDropdown.selectedIndex].text;
			// setTimeout(() => {

			// }, timeout);
			const printWindow = window.open('', '_blank');
			printWindow.document.write(`
                        <html>
						<head>
						<img src="src/logo.png" class="w-32" alt="Ali Baba Travel Advisor"/>
                                <title>Documents for ${countryName}</title>
                                <style>
                                    body { font-family: Arial, sans-serif; margin: 20px; }
                                    h1 { text-align: center; }
                                    ul { list-style-type: disc; padding-left: 20px; }
                                </style>
                            </head>
                            <body>
                                <h1>Documents for ${countryName}</h1>
                                <ul>
                                    ${documents.map((doc) => `<li>${doc.document}</li>`).join('')}
                                </ul>
                            </body>
                        </html>
                    `);

			printWindow.document.close();
			setTimeout(() => {
				printWindow.print();
			}, 100);
		});
	}
});
