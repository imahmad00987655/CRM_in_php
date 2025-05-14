<div class="flex flex-wrap p-12 bg-red-200 bg-opacity-70 gap-10 w-full h-full items-start justify-between overflow-y-scroll">
    <div class="font-semibold text-3xl w-full justify-between flex items-cente text-red-600 bg-white bg-opacity-50 rounded-xl p-5">
        New Officer</div>
    <div class="flex flex-wrap w-full gap-2">
        <div class="flex flex-col w-full sm:w-1/2 md:w-1/3 gap-2">
            <p class="text-base w-fit text-nowrap capitalize">name</p>
            <input type="text" id="officer_name" placeholder="Officer Name" class="w-full bg-white rounded-lg text-lg ring-1 ring-red-300 outline-none focus:outline-none focus:border-none focus:ring-2 px-3 py-2 mb-2" />
        </div>
        <div class="flex flex-col w-full sm:w-1/2 md:w-1/3 gap-2">
            <p class="text-base w-fit text-nowrap capitalize">email</p>
            <input type="email" id="officer_email" placeholder="Email" class="w-full bg-white rounded-lg text-lg ring-1 ring-red-300 outline-none focus:outline-none focus:border-none focus:ring-2 px-3 py-2 mb-2" />
        </div>
        <div class="flex flex-col w-full sm:w-1/2 md:w-1/3 gap-2">
            <p class="text-base w-fit text-nowrap capitalize">phone number</p>
            <input type="number" id="officer_phone_number" placeholder="Phone Number" class="w-full bg-white rounded-lg text-lg ring-1 ring-red-300 outline-none focus:outline-none focus:border-none focus:ring-2 px-3 py-2 mb-2" />
        </div>
        <div class="flex flex-col w-full sm:w-1/2 md:w-1/3 gap-2">
            <p class="text-base w-fit text-nowrap capitalize">CNIC</p>
            <input type="text" id="officer_cnic" placeholder="CNIC" class="w-full bg-white rounded-lg text-lg ring-1 ring-red-300 outline-none focus:outline-none focus:border-none focus:ring-2 px-3 py-2 mb-2" />
        </div>
        <div class="flex flex-col w-full sm:w-1/2 md:w-1/3 gap-2">
            <p class="text-base w-fit text-nowrap capitalize">user role</p>
            <select id="officer_user_role" class="w-full bg-white rounded-lg text-lg ring-1 ring-red-300 outline-none focus:outline-none focus:border-none focus:ring-2 px-3 py-2 mb-2">
            </select>
        </div>
        <div class="flex flex-col w-full sm:w-1/2 md:w-1/3 gap-2">
            <p class="text-base w-fit text-nowrap capitalize">date of birth</p>
            <input type="date" id="officer_date_of_birth" class="w-full bg-white rounded-lg text-lg ring-1 ring-red-300 outline-none focus:outline-none focus:border-none focus:ring-2 px-3 py-2 mb-2" />
        </div>
        <div class="flex flex-col w-full sm:w-1/2 md:w-1/3 gap-2">
            <p class="text-base w-fit text-nowrap capitalize">gender</p>
            <select class="w-full bg-white rounded-lg text-lg ring-1 ring-red-300 outline-none focus:outline-none focus:border-none focus:ring-2 px-3 py-2 mb-2" id="officer_gender">
                <option value="">Gender</option>
                <option value="Male">Male</option>
                <option value="Female">Female</option>
                <option value="Other">Other</option>
            </select>
        </div>
        <div class="flex flex-col w-full sm:w-1/2 md:w-1/3 gap-2">
            <p class="text-base w-fit text-nowrap capitalize">city</p>
            <select class="w-full bg-white rounded-lg text-lg ring-1 ring-red-300 outline-none focus:outline-none focus:border-none focus:ring-2 px-3 py-2 mb-2" id="officer_city">
            </select>
        </div>
        <div class="flex flex-col w-full sm:w-1/2 md:w-1/3 gap-2">
            <p class="text-base w-fit text-nowrap capitalize">user name</p>
            <input type="text" id="officer_username" placeholder="User Name" class="w-full bg-white rounded-lg text-lg ring-1 ring-red-300 outline-none focus:outline-none focus:border-none focus:ring-2 px-3 py-2 mb-2" />
        </div>
        <div class="flex flex-col w-full sm:w-1/2 md:w-1/3 gap-2">
            <p class="text-base w-fit text-nowrap capitalize">password</p>
            <input type="password" id="officer_password" placeholder="Password" class="w-full bg-white rounded-lg text-lg ring-1 ring-red-300 outline-none focus:outline-none focus:border-none focus:ring-2 px-3 py-2 mb-2" />
        </div>
    </div>
    <button class="text-xl px-10 py-3 place-self-end  bg-red-600 text-white font-medium rounded-full flex items-center justify-center gap-10 hover:bg-black cursor-pointer" onclick="saveOrUpdateOfficer()">Save</button>
</div>