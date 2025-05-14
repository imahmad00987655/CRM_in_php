<?php
$user_role =  $_GET['user_role'];
?>

<div class="flex flex-col gap-10 p-5 sm:p-12 bg-red-200 bg-opacity-70 w-full h-full items-start justify-between overflow-y-scroll">
    <!-- personal information -->
    <div class="font-semibold text-3xl w-full justify-between flex flex-col sm:flex-row gap-5 h-fit sm:items-center text-red-600 bg-white bg-opacity-50 rounded-xl p-5">

        Personal Information
        <?php if (in_array($user_role , ['admin','data_entry_agent'])): ?>
            <button type="button" onclick="loadComponent('newApplication.php', 'New Application')" id="clearBtn" class="w-fit py-1 text-base px-3 rounded-lg cursor-pointer bg-black hover:bg-red-600 text-white">Clear</button>
        <?php endif; ?>
    </div>
    <?php include '../backend/fetch_cities_data.php'; ?>
    <div class="flex flex-col sm:flex-row justify-between gap-10 w-full h-fit py-5 my-5">
        <div class="flex flex-col gap-10 w-full h-fit">
            <!-- Name input -->
            <div class="flex gap-2 items-center w-full">
                <p for="applicant_name" class="text-xl w-32">First Name</p>
                <input
                    type="text"
                    id="applicant_name"
                    name="applicant_name"
                    placeholder="Type Name..."
                    class="w-full bg-white rounded-lg text-lg ring-1 ring-red-300 outline-none focus:outline-none focus:border-none focus:ring-2 px-3 py-2" />
            </div>
            <div class="flex gap-2 items-center w-full">
                <p for="applicant_name" class="text-xl w-32">Surname</p>
                <input
                        type="text"
                        id="applicant_surname"
                        name="applicant_surname"
                        placeholder="Type Name..."
                        class="w-full bg-white rounded-lg text-lg ring-1 ring-red-300 outline-none focus:outline-none focus:border-none focus:ring-2 px-3 py-2" />
            </div>
            <div class="flex gap-2 items-center w-full">
                <p for="number" class="text-xl w-32">Number</p>
                <input type="text" id="phone_number" name="phone_number" placeholder="Type Phone Number..."
                    class="w-full bg-white rounded-lg text-lg ring-1 ring-red-300 outline-none focus:outline-none focus:border-none focus:ring-2 px-3 py-2" />
            </div>
            <!-- Amount Field -->
            <div class="flex gap-2 items-center w-full">
                <p class="text-xl w-32">Amount</p>
                <input type="number" step="1000" autocomplete="off" min="0" id="total_amount" name="total_amount" placeholder="Type Amount..."
                    class="w-full bg-white rounded-lg text-lg ring-1 ring-red-300 outline-none focus:outline-none focus:border-none focus:ring-2 px-3 py-2" />
            </div>
            <!-- Advance Field -->
<!--            --><?php //if ($user_role == 'admin'): ?>
                <div class="flex gap-2 items-center w-full">
                    <p class="text-xl w-32">Adv. Amount</p>
                    <input type="number" step="1000" autocomplete="off" min="0" id="advance_amount" name="advance_amount" placeholder="Type Amount..."
                        class="w-full bg-white rounded-lg text-lg ring-1 ring-red-300 outline-none focus:outline-none focus:border-none focus:ring-2 px-3 py-2" />
                </div>
<!--            --><?php //endif; ?>
            <!-- CNIC Field -->
            <div class="flex gap-2 items-center w-full">
                <p for="cnic" class="text-xl w-32">CNIC</p>
                <input type="text" id="applicant_cnic" name="applicant_cnic" placeholder="Type CNIC Number..."
                    class="w-full bg-white rounded-lg text-lg ring-1 ring-red-300 outline-none focus:outline-none focus:border-none focus:ring-2 px-3 py-2" />
            </div>
            <!-- address -->
            <div class="flex gap-2 items-center w-full">
                <p for="applicant_address" class="text-xl w-32">Address</p>
                <input type="text" id="applicant_address" name="applicant_address" placeholder="Type Address..."
                    class="w-full bg-white rounded-lg text-lg ring-1 ring-red-300 outline-none focus:outline-none focus:border-none focus:ring-2 px-3 py-2" />
            </div>
            <!-- Passport Number Field -->
            <div class="flex flex-col sm:flex-row gap-2 sm:gap-10 sm:items-center w-full">
                <p for="passport-number" class="text-xl w-fit text-nowrap">Passport Number</p>
                <input type="text" id="passport_number" name="passport_number"
                    placeholder="Type Passport Number..."
                    class="w-full bg-white rounded-lg text-lg ring-1 ring-red-300 outline-none focus:outline-none focus:border-none focus:ring-2 px-3 py-2" />
            </div>
            <!-- application_limit -->
<!--            <div class="flex flex-col sm:flex-row gap-2 h-fit sm:gap-10 sm:items-center w-full">-->
<!--                <p for="application_limit" class="text-xl w-fit text-nowrap">Application limit</p>-->
<!--                <input type="number" id="application_limit" name="application_limit"-->
<!--                    class="w-32 bg-white rounded-lg text-lg ring-1 ring-red-300 outline-none focus:outline-none focus:border-none focus:ring-2 px-3 py-2" />-->
<!--            </div>-->

<!--            Deadline date-->
            <div class="flex flex-col sm:flex-row gap-2 h-fit sm:gap-10 sm:items-center w-full">
                <p for="deadline_date" class="text-xl w-fit text-nowrap">Deadline Date</p>
                <input type="date" id="deadline_date" name="deadline_date"
                       class="w-full bg-white rounded-lg text-lg ring-1 ring-red-300 outline-none focus:outline-none focus:border-none focus:ring-2 px-3 py-2" />
            </div>
            <!-- Submit Button -->
            <?php if ($user_role == 'admin'): ?>
                <button id="save_form" type="submit"
                    class="text-xl px-10 py-3 place-self-end  bg-red-600 text-white font-medium rounded-full flex items-center justify-center gap-10 hover:bg-black cursor-pointer">
                    Assign to Data Entry
                    <span>»</span>
                </button>
            <?php endif; ?>
        </div>
        <div class="flex flex-col gap-5 w-fit h-fit">
            <!-- Country Dropdown -->
            <div class="flex flex-col gap-3 w-fit">
                <p for="application_country_id" class="text-xl w-32">Country</p>
                <select name="application_country_id" id="application_country_id"
                    class="w-72 bg-white text-lg ring-1 ring-red-300 focus:ring-2 hover:ring-2 py-2 px-3 text-gray-700 focus:outline-none rounded-lg">
                    <option value="">Choose option</option>
                    <?php foreach ($countries as $country): ?>
                        <option value="<?php echo  $country['id'] ?>">
                            <?php echo $country["name"]; ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <!-- Application City Dropdown -->
            <div class="flex flex-col gap-3 w-fit">
                <p for="application_city" class="text-xl">Application City</p>
                <select name="application_city" id="application_city"
                    onchange="fetchAgentsAndDataEntries(this.value)"
                    class="w-72 bg-white text-lg ring-1 ring-red-300 focus:ring-2 hover:ring-2 py-2 px-3 text-gray-700 focus:outline-none rounded-lg">

                    <option>Choose option</option>
                    <?php foreach ($cities as $city): ?>
                        <option value="<?php echo $city['id']; ?>" <?php echo !empty($selected_city_id) && $city['id'] == $selected_city_id ? 'selected' : ''; ?> ><?php echo $city['city_name']; ?></option>

                    <?php endforeach; ?>
                </select>
            </div>
            <!-- Occupation Dropdown -->
            <div class="flex flex-col gap-3 w-fit">
                <p class="text-xl">Occupation</p>
                <select id="occupation" name="occupation"
                    class="w-72 bg-white text-lg ring-1 ring-red-300 focus:ring-2 hover:ring-2 py-2 px-3 text-gray-700 focus:outline-none rounded-lg">
                    <option>Choose option</option>
                    <option value="Job Person">Job Person</option>
                    <option value="Business Man">Business Man</option>
                    <option value="Agriculture">Agriculture</option>
                    <option value="Land Lord">Land Lord</option>
                    <option value="Student">Student</option>
                    <option value="House-Wife">House-Wife</option>
                    <option value="Child">Child</option>
                </select>
            </div>
            <!-- Persons Dropdown -->
            <div class="flex flex-col gap-3 w-fit">
                <p for="persons" class="text-xl">Persons</p>
                <select id="persons" name="persons"
                    class="w-72 bg-white text-lg ring-1 ring-red-300 focus:ring-2 hover:ring-2 py-2 px-3 text-gray-700 focus:outline-none rounded-lg">
                    <option value="">Choose option</option>
                    <option value="single_person">Single Person</option>
                    <option value="couple">Two Persons</option>
                    <option value="family-3">Three Persons</option>
                    <option value="family-4">Four Persons</option>
                </select>
            </div>

            <!-- Agent Dropdown -->
            <div class="flex flex-col gap-3 w-fit">
                <p for='visa_agent' class="text-xl">Agent</p>
                <select name="visa_agent" id="visa_agent"
                    class="w-72 bg-white text-lg ring-1 ring-red-300 focus:ring-2 hover:ring-2 py-2 px-3 text-gray-700 focus:outline-none rounded-lg">
                    <option>Choose option</option>
                </select>
            </div>
            <!-- Data Entry Dropdown -->
            <div class="flex flex-col gap-3 w-fit">
                <p for="data_entry_agent" class="text-xl">Data Entry</p>
                <select name="data_entry_agent" id="data_entry_agent"
                    class="w-72 bg-white text-lg ring-1 ring-red-300 focus:ring-2 hover:ring-2 py-2 px-3 text-gray-700 focus:outline-none rounded-lg">
                    <option value="default">Choose option</option>
                </select>
            </div>
        </div>
    </div>
    <div id="personContainer" class="h-fit w-full"></div>
    <!-- traveling plan -->
    <div class="font-semibold text-3xl w-full justify-between flex flex-col gap-5 text-red-600 bg-white bg-opacity-50 rounded-xl p-5">
        Traveling Plan
        <div class="flex flex-col sm:flex-row text-lg text-black gap-5 w-full sm:w-1/2 h-fit justify-between font-medium">
            <p>Start Date</p>
            <input type="date" id="taveling_start" class="w-fit px-2 py-1 border-2  border-red-300  rounded-lg">
        </div>
        <div class="flex flex-col sm:flex-row text-lg text-black gap-5 w-full sm:w-1/2 h-fit justify-between font-medium">
            <p>End Date</p>
            <input type="date" id="taveling_end" class="w-fit px-2 py-1 border-2  border-red-300 rounded-lg">
        </div>
        <div class="flex flex-col sm:flex-row text-lg text-nowrap text-black gap-5 w-full h-fit justify-between font-medium">
            <p>Special Request</p>
            <input type="text" id="special_request" class="w-full px-2 py-1 border-red-300 border-b-2 rounded-lg">
        </div>
    </div>
    <!-- extra info -->
    <div class="font-semibold text-3xl w-full justify-between flex flex-col gap-5 text-red-600 bg-white bg-opacity-50 rounded-xl p-5">
        Extra Information
        <input type="text" id="extra_info" name="extra_info" class="text-lg text-black w-full px-2 py-1 border-red-300 border-b-2 rounded-lg">
    </div>
    <!-- proceed to agent -->
    <div class="flex flex-col sm:flex-row w-full items-center justify-end gap-10">
        <?php if ($user_role !== 'visa_agent'): ?>
            <div class="flex items-center w-fit gap-5 text-2xl">
                <input type="checkbox" id="proceed_to_agent" name="proceed_to_agent" class="min-h-6 min-w-6" />
                Proceed to Visa Agent
            </div>
            <button id="save_form" type="submit"
                class="text-xl px-10 py-3 place-self-end  bg-red-600 text-white font-medium rounded-full flex items-center justify-center gap-10 hover:bg-black cursor-pointer">
                <span>Save</span>
                <span>»</span>
            </button>
        <?php endif; ?>

    </div>
</div>

<script>
    var city_id_ = "<?php echo $selected_city_id ?? '' ?>"

    if(city_id_){
      setTimeout(function() {
        fetchAgentsAndDataEntries(city_id_);
      }, 1000);
    }

</script>