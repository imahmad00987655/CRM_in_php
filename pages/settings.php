<?php
include '../backend/fetch_cities_data.php';
$user_role =  $_GET['user_role'];
?>

<div class="max-w-full h-fit m-10 bg-white bg-opacity-70 flex flex-col gap-10 p-5 rounded-3xl overflow-hidden">
    <!-- Navigation Buttons -->
    <div class="flex gap-4 mb-5 overflow-scroll scrollbar-none w-full h-14">
        <button onclick="showSection('basic-info')" class="w-fit text-nowrap px-2.5 py-1.5 rounded-lg bg-red-600 text-white hover:bg-black hover:text-white">Basic Info</button>
        <?php if ($user_role == 'admin'): ?>
            <button onclick="showSection('country-amount')" class="w-fit px-2.5 text-nowrap py-1.5 rounded-lg bg-red-600 text-white hover:bg-black hover:text-white">Country Wise Amount</button>
            <button onclick="showSection('cities-data')" class="w-fit px-2.5 py-1.5 text-nowrap rounded-lg bg-red-600 text-white hover:bg-black hover:text-white">Cities</button>
            <button onclick="showSection('countries-data')" class="w-fit px-2.5 py-1.5 text-nowrap rounded-lg bg-red-600 text-white hover:bg-black hover:text-white">Countries</button>
        <?php endif; ?>
    </div>

    <!-- Basic Info Section -->
    <div id="basic-info" class="content-section flex flex-wrap gap-2 w-full h-full p-1 overflow-scroll scrollbar-none">
        <div class="flex flex-col w-full sm:w-1/2 md:w-1/3 gap-2">
            <p class="text-base w-fit text-nowrap capitalize">name</p>
            <input id="logged_name" type="text" placeholder="Name" class="w-full bg-white rounded-lg text-lg ring-1 ring-red-300 outline-none focus:outline-none focus:border-none focus:ring-2 px-3 py-2 mb-2" value="<?= htmlspecialchars($_SESSION['name'] ?? '') ?>" />
        </div>
        <div class="flex flex-col w-full sm:w-1/2 md:w-1/3 gap-2">
            <p class="text-base w-fit text-nowrap capitalize">email</p>
            <input id="logged_email" type="email" placeholder="Email" class="w-full bg-white rounded-lg text-lg ring-1 ring-red-300 outline-none focus:outline-none focus:border-none focus:ring-2 px-3 py-2 mb-2" value="<?= htmlspecialchars($_SESSION['email'] ?? '') ?>" />
        </div>
        <div class="flex flex-col w-full sm:w-1/2 md:w-1/3 gap-2">
            <p class="text-base w-fit text-nowrap capitalize">phone number</p>
            <input id="logged_phone_number" type="number" placeholder="Phone Number" class="w-full bg-white rounded-lg text-lg ring-1 ring-red-300 outline-none focus:outline-none focus:border-none focus:ring-2 px-3 py-2 mb-2" value="<?= htmlspecialchars($_SESSION['phone_number'] ?? '') ?>" />
        </div>
        <div class="flex flex-col w-full sm:w-1/2 md:w-1/3 gap-2">
            <p class="text-base w-fit text-nowrap capitalize">user name</p>
            <input id="logged_user_name" type="text" placeholder="User Name" class="w-full bg-white rounded-lg text-lg ring-1 ring-red-300 outline-none focus:outline-none focus:border-none focus:ring-2 px-3 py-2 mb-2" value="<?= htmlspecialchars($_SESSION['username'] ?? '') ?>" />
        </div>
        <div class="flex flex-col w-full sm:w-1/2 md:w-1/3 gap-2">
            <p class="text-base w-fit text-nowrap capitalize">date of birth</p>
            <input id="logged_date_of_birth" type="date" placeholder="DOB" class="w-full bg-white rounded-lg text-lg ring-1 ring-red-300 outline-none focus:outline-none focus:border-none focus:ring-2 px-3 py-2 mb-2" value="<?= htmlspecialchars($_SESSION['date_of_birth'] ?? '') ?>" />
        </div>
        <div class="flex flex-col w-full sm:w-1/2 md:w-1/3 gap-2">
            <p class="text-base w-fit text-nowrap capitalize">gender</p>
            <select id="logged_gender" class="w-full bg-white rounded-lg text-lg ring-1 ring-red-300 outline-none focus:outline-none focus:border-none focus:ring-2 px-3 py-2 mb-2">
                <option value="Male" <?= ($_SESSION['gender'] == 'Male') ? 'selected' : '' ?>>Male</option>
                <option value="Female" <?= ($_SESSION['gender'] == 'Female') ? 'selected' : '' ?>>Female</option>
                <option value="Other" <?= ($_SESSION['gender'] == 'Other') ? 'selected' : '' ?>>Other</option>
            </select>
        </div>
        <!--if ($user_role == 'admin')  -->
        <!-- <? php: ?> -->
        <div class="flex flex-col w-full sm:w-1/2 md:w-1/3 gap-2">
            <p class="text-base w-fit text-nowrap capitalize">user role</p>
            <select id="logged_roles" class="w-full bg-white rounded-lg text-lg ring-1 ring-red-300 outline-none focus:outline-none focus:border-none focus:ring-2 px-3 py-2 mb-2">
            </select>
        </div>
        <div class="flex flex-col w-full sm:w-1/2 md:w-1/3 gap-2">
            <p class="text-base w-fit text-nowrap capitalize">CNIC</p>
            <input id="logged_cnic" type="text" placeholder="CNIC" class="w-full bg-white rounded-lg text-lg ring-1 ring-red-300 outline-none focus:outline-none focus:border-none focus:ring-2 px-3 py-2 mb-2" value="<?= htmlspecialchars($_SESSION['cnic'] ?? '') ?>" />
        </div>
        <div class="flex flex-col w-full sm:w-1/2 md:w-1/3 gap-2">
            <p class="text-base w-fit text-nowrap capitalize">city</p>
            <select id="logged_city" class="w-full bg-white rounded-lg text-lg ring-1 ring-red-300 outline-none focus:outline-none focus:border-none focus:ring-2 px-3 py-2 mb-2">
            </select>
        </div>
        <!-- endif; -->
        <!-- <?php ?> -->
        <div class="flex flex-col w-full sm:w-1/2 md:w-1/3 gap-2">
            <p class="text-base w-fit text-nowrap capitalize">password</p>
            <input id="logged_password" type="password" placeholder="Password" class="w-full bg-white rounded-lg text-lg ring-1 ring-red-300 outline-none focus:outline-none focus:border-none focus:ring-2 px-3 py-2 mb-2" />
        </div>
    </div>

    <?php if ($user_role == 'admin'): ?>
        <!-- Country Wise Amount Section -->
        <div id="country-amount" class="content-section hidden flex-col gap-5 w-full h-full p-1 overflow-scroll scrollbar-none">
            <div class="flex flex-col w-full sm:w-1/2 gap-2">
                <p class="text-base w-fit text-nowrap capitalize">Country</p>
                <select id="country_id" class="w-full bg-white rounded-lg text-lg ring-1 ring-red-300 outline-none focus:outline-none focus:border-none focus:ring-2 px-3 py-2 mb-2" onchange="getPricingData(this.value)">
                    <option value="">Choose Country</option>
                    <?php foreach ($countries as $country): ?>
                        <option value="<?php echo $country['id']; ?>"><?php echo $country['name']; ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="flex flex-col w-full sm:w-1/2 gap-2">
                <p class="text-base w-fit text-nowrap capitalize">Single Person</p>
                <input id="single_person" type="number" placeholder="Single Person" class="w-full bg-white rounded-lg text-lg ring-1 ring-red-300 outline-none focus:outline-none focus:border-none focus:ring-2 px-3 py-2 mb-2" />
            </div>
            <div class="flex flex-col w-full sm:w-1/2 gap-2">
                <p class="text-base w-fit text-nowrap capitalize">Couple</p>
                <input id="couple" type="number" placeholder="Couple" class="w-full bg-white rounded-lg text-lg ring-1 ring-red-300 outline-none focus:outline-none focus:border-none focus:ring-2 px-3 py-2 mb-2" />
            </div>
            <div class="flex flex-col w-full sm:w-1/2 gap-2">
                <p class="text-base w-fit text-nowrap capitalize">3 Person</p>
                <input id="family_3" type="number" placeholder="3 Persons" class="w-full bg-white rounded-lg text-lg ring-1 ring-red-300 outline-none focus:outline-none focus:border-none focus:ring-2 px-3 py-2 mb-2" />
            </div>
            <div class="flex flex-col w-full sm:w-1/2 gap-2">
                <p class="text-base w-fit text-nowrap capitalize">4 Person</p>
                <input id="family_4" type="number" placeholder="4 Persons" class="w-full bg-white rounded-lg text-lg ring-1 ring-red-300 outline-none focus:outline-none focus:border-none focus:ring-2 px-3 py-2 mb-2" />
            </div>
        </div>
        <!-- New City -->
        <div id="cities-data" class="content-section hidden flex-col gap-5 w-full h-full p-1 overflow-scroll scrollbar-none">
            <div class="flex flex-col w-full sm:w-1/2 gap-5">
                <p class="text-xl font-semibold capitalize">Cities</p>
                <ul id="cityList" class="flex flex-col gap-3"></ul>
            </div>
            <div class="flex flex-col w-full sm:w-1/2 gap-5">
                <p class="text-xl font-semibold capitalize">New City</p>
                <input
                    id="cityName"
                    type="text"
                    placeholder="Enter city name"
                    class="w-full bg-white rounded-lg text-lg ring-1 ring-red-300 outline-none focus:outline-none focus:border-none focus:ring-2 px-3 py-2 mb-2" />
                <button
                    onclick="addCity()"
                    class="w-full bg-red-500 text-white py-2 rounded-lg hover:bg-red-600 transition">
                    Add City
                </button>
            </div>
        </div>
        <!-- New Country -->
        <div id="countries-data" class="content-section hidden flex-col gap-5 w-full h-full p-1 overflow-scroll scrollbar-none">
            <div class="flex flex-col w-full sm:w-1/2 gap-5">
                <p class="text-xl font-semibold capitalize">Countries</p>
                <ul id="countryList" class="flex flex-col gap-3"></ul>
            </div>
            <div class="flex flex-col w-full sm:w-1/2 gap-5">
                <p class="text-xl font-semibold capitalize">New Country</p>
                <input
                    id="countryName"
                    type="text"
                    placeholder="Enter country name"
                    class="w-full bg-white rounded-lg text-lg ring-1 ring-red-300 outline-none focus:outline-none focus:border-none focus:ring-2 px-3 py-2 mb-2" />
                <button
                    onclick="addCountry()"
                    class="w-full bg-red-500 text-white py-2 rounded-lg hover:bg-red-600 transition">
                    Add Country
                </button>
            </div>
        </div>

    <?php endif; ?>
    <button id="submit_settings" class="text-xl px-10 py-3 place-self-end  bg-red-600 text-white font-medium rounded-full flex items-center justify-center gap-10 hover:bg-black cursor-pointer" onclick="handleSubmit()">Save</button>
</div>

<script>
    var citiesUrl = "../backend/cities_controller.php"; // Adjust path as needed
    var section = 'basic-info';
    var countriesUrl = "../backend/countries_controller.php";

    onMounted();

    function onMounted() {
      const fieldIds = ['logged_name', 'logged_email', 'logged_phone_number', 'logged_user_name', 'logged_date_of_birth', 'logged_gender', 'logged_roles', 'logged_cnic', 'logged_city'];

      if (userRole === 'sales_agent' || userRole === 'manager' || userRole === 'visa_agent' || userRole === 'data_entry_agent') {
        fieldIds.forEach((id) => {
          const element = document.getElementById(id);
          if (element) {
            element.setAttribute('disabled', 'true');
          } else {
            console.log(`Element with ID ${id} not found.`);
          }
        });
      }
    }

    function showSection(sectionId) {
        // Hide all sections
        document.querySelectorAll('.content-section').forEach(section => section.classList.add('hidden'));
        document.querySelectorAll('.content-section').forEach(section => section.classList.remove('flex'));

        // Show the selected section
        const activeSection = document.getElementById(sectionId);
        section = (sectionId)
        activeSection.classList.remove('hidden');
        activeSection.classList.add('flex');
        document.getElementById('submit_settings').classList.remove('hidden');

        if (sectionId === 'basic-info') {
            fetchRoles();
            fetchCities();
        }
        if (sectionId === 'cities-data') {
            fetchCities();
            document.getElementById('submit_settings').classList.add('hidden');
        }
        if (sectionId === 'countries-data') {
            fetchCountries();
            document.getElementById('submit_settings').classList.add('hidden');
        }
    }
    fetchRoles();
    fetchCities();

    function handleSubmit() {
        const activeSection = document.querySelector('.content-section:not(.hidden)');
        if (activeSection.id === 'country-amount') {
            savePricingData();
        }
        if (activeSection.id === 'add-officer') {
            saveOfficer();
        }
        if (activeSection.id === 'basic-info') {
            updateProfile();
        }
    }

    function fetchRoles() {
        var selectedValue = '<?= $_SESSION['user_role'] ?? '' ?>' ?? '';
        fetch(`../backend/officers_controller.php?fetch=${'roles'}`)
            .then(response => response.json())
            .then(data => {
                const select = document.getElementById('logged_roles');
                select.innerHTML = `<option>Select ${'roles'.charAt(0).toUpperCase() + 'roles'.slice(1)}</option>`;
                data.forEach(item => {
                    let formattedName = item?.role_name.replace(/_/g, ' ') // Replace underscores with spaces
                        .replace(/\b\w/g, char => char.toUpperCase()); // Capitalize each word
                    let isSelected = item?.role_name === selectedValue ? 'selected' : '';
                    select.innerHTML += `<option value="${item.id}" ${isSelected}>${formattedName}</option>`;
                });

                if(selectedValue === 'manager'){
                  select.innerHTML += `<option value="5" selected>Manager</option>`;
                }
            })
            .catch(error => console.error(`Error fetching ${type}:`, error));
    }

    function getPricingData(country_id) {
        if (!country_id) return;
        fetch('../backend/fetch_save_pricing.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    action: 'getPricing',
                    country_id
                })
            })
            .then(response => response.json())
            .then(data => {
                document.getElementById("single_person").value = data.single_person || "";
                document.getElementById("couple").value = data.couple || "";
                document.getElementById("family_3").value = data.family_3 || "";
                document.getElementById("family_4").value = data.family_4 || "";
            })
            .catch(error => console.error("Error fetching pricing:", error));
    }

    function savePricingData() {
        const data = {
            action: "updatePricing",
            country_id: document.getElementById("country_id").value,
            single_person: document.getElementById("single_person").value,
            couple: document.getElementById("couple").value,
            family_3: document.getElementById("family_3").value,
            family_4: document.getElementById("family_4").value
        };

        fetch('../backend/fetch_save_pricing.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(data)
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert("Pricing updated successfully!");
                    clearForm()
                }
            })
            .catch(error => console.error("Error updating pricing:", error));
    }

    function updateProfile() {
        const data = {
            name: document.getElementById("logged_name").value.trim() ?? null,
            email: document.getElementById("logged_email").value.trim() ?? null,
            phone_number: document.getElementById("logged_phone_number").value.trim() ?? null,
            cnic: document.getElementById("logged_cnic").value.trim() ?? null,
            user_role: document.getElementById("logged_roles").value.trim() ?? null,
            date_of_birth: document.getElementById("logged_date_of_birth").value.trim() ?? null,
            gender: document.getElementById("logged_gender").value.trim() ?? null,
            city: document.getElementById("logged_city").value.trim() ?? null,
            username: document.getElementById("logged_user_name").value.trim() ?? null,
            password: document.getElementById("logged_password").value.trim() ?? null
        }
        // Validate Required Fields
        if (!data.name || !data.email || !data.username || !data.user_role) {
            alert("Please fill in all required fields!");
            return;
        }

        fetch('../backend/update_profile.php', {
                method: "POST",
                headers: {
                    "Content-Type": "application/json"
                },
                body: JSON.stringify(data)
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === "success") {
                    alert("Profile updated successfully!");
                    location.reload();
                } else {
                    alert("Error: " + data.message);
                }
            })
            .catch(error => {
                console.error("Error:", error);
                alert("Something went wrong! Please try again.");
            });
    }

    function clearForm() {
        document.querySelectorAll("input, select, textarea").forEach(element => {
            if (element.tagName === "SELECT") {
                element.selectedIndex = 0; // Reset select dropdown to first option
            } else {
                element.value = ""; // Clear input and textarea fields
            }
        });
    }
    // Fetch Cities from Database
    async function fetchCities() {
        const response = await fetch(citiesUrl + "?fetch=cities");
        const data = await response.json();
        if (data.status === "success") {
            if (section == 'basic-info') {
                var selectedValue = '<?= $_SESSION["city"] ?? '' ?>';
                const select = document.getElementById('logged_city');
                select.innerHTML = `<option value=''>Select city</option>`;
                data.cities.forEach(item => {
                    let isSelected = item.id == selectedValue;
                    select.innerHTML += `<option value="${item.id}" ${isSelected?'selected':""}>${item.city_name}</option>`;
                });
                return;
            }
            const cityList = document.getElementById("cityList");
            cityList.innerHTML = "";
            data.cities.forEach(city => {
                cityList.innerHTML += `<li class="w-full px-2 py-2 bg-white rounded-xl flex items-center gap-2">
                <span class="w-full text-lg">${city.city_name}</span>
                        <button class="w-fit px-2 py-1 rounded-lg text-white bg-red-500 hover:bg-red-700" onclick="deleteCity(${city.id})">Delete</button>
                    </li>`;
            });
        } else {
            alert("Error fetching cities: " + data.message);
        }
    }
    // Add City
    async function addCity() {
        const city_name = document.getElementById("cityName").value;
        if (!city_name) {
            alert("City name cannot be empty!");
            return;
        }

        const response = await fetch(citiesUrl, {
            method: "POST",
            headers: {
                "Content-Type": "application/json"
            },
            body: JSON.stringify({
                action: "save_city",
                city_name
            })
        });

        const result = await response.json();
        alert(result.message);
        fetchCities();
        clearForm(); // Clear the input field after successful addition
    }
    // Delete City
    async function deleteCity(id) {
        if (!confirm("Are you sure you want to delete this city?")) return;

        const response = await fetch(citiesUrl, {
            method: "POST",
            headers: {
                "Content-Type": "application/json"
            },
            body: JSON.stringify({
                action: "delete_city",
                id
            })
        });

        const result = await response.json();
        alert(result.message);
        fetchCities();
    }
    // Fetch Countries from Database
    async function fetchCountries() {
        const response = await fetch(countriesUrl + "?fetch=countries");
        const data = await response.json();
        if (data.status === "success") {
            const countryList = document.getElementById("countryList");
            countryList.innerHTML = "";
            data.countries.forEach(country => {
                countryList.innerHTML += `<li class="w-full px-2 py-2 bg-white rounded-xl flex items-center gap-2">
                <span class="w-full text-lg">${country.name}</span>
                <button class="w-fit px-2 py-1 rounded-lg text-white bg-red-500 hover:bg-red-700" onclick="deleteCountry(${country.id})">Delete</button>
            </li>`;
            });
        } else {
            alert("Error fetching countries: " + data.message);
        }
    }

    // Add Country
    async function addCountry() {
        const country_name = document.getElementById("countryName").value;
        if (!country_name) {
            alert("Country name cannot be empty!");
            return;
        }

        const response = await fetch(countriesUrl, {
            method: "POST",
            headers: {
                "Content-Type": "application/json"
            },
            body: JSON.stringify({
                action: "save_country",
                name: country_name
            })
        });

        const result = await response.json();
        alert(result.message);
        fetchCountries();
        clearForm(); // Clear the input field after successful addition
    }

    // Delete Country
    async function deleteCountry(id) {
        if (!confirm("Are you sure you want to delete this country?")) return;

        const response = await fetch(countriesUrl, {
            method: "POST",
            headers: {
                "Content-Type": "application/json"
            },
            body: JSON.stringify({
                action: "delete_country",
                id
            })
        });

        const result = await response.json();
        alert(result.message);
        fetchCountries();
    }
</script>