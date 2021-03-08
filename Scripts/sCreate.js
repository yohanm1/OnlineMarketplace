let strengthLabels = [
	"Very Weak",
	"Weak",
	"Fair",
	"Strong",
	"Very Strong"
];

let usernameExists = false;

let validation = {
	username: [ //the array of tests to run against usernames
		minLength(3, "Username too short"),
		maxLength(10, "Username too long"),
		regex(/^[0-9a-zA-Z]+$/,"Username can only be letters and numbers"),
		available("Username exists")
	],
	password: [
		minLength(1,"Password too short"),
		confirmed("Passwords must match"),
		strength(2,"Password not strong enough")
	],
	name: [
		minLength(1,"Name too short")
	],
	email: [
		minLength(1, "Email too short"),
		emailAvailable("Email already in use")
	]
};

let validate = (id) => {
	let validators = validation[id];
	let value = document.getElementById(id).value;
	for (let i = 0; i < validators.length; i++){
		let result = validators[i](value);
		if (result !== true)
			return result;
	}
	return true;
};

let submitForm = function(e){
	let ids = ['username', 'password', 'sName', 'email'];

	for (let i = 0; i < ids.length; i++)
	{
		let id = ids[i];
		let result = validate(id);
		if (result != true)
		{
			showError(result);

			e.preventDefault();
			return false;
		}
	}

	return true;
};

window.addEventListener("load", function(){
	//register submit handler
	let formNode = document.getElementById('form');
	formNode.addEventListener('submit',submitForm);

	//grab any error messages returned by php in the # field
	let errorMessage = window.location.hash;
	window.location.hash = "";
	console.log('error message is'+errorMessage);

	//if an error from php exists, show it
	if(errorMessage && errorMessage.length > 0){
		errorMessage = decodeURIComponent(errorMessage).slice(1);
		showError(errorMessage);
	}
});