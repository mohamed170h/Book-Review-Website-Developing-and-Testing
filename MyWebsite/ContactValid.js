document.getElementById('contactForm').addEventListener('submit', function(event) {
    // Prevent the form from submitting
    event.preventDefault();

    // Get the form fields
    var name = document.getElementById('name');
    var email = document.getElementById('email');
    var message = document.getElementById('message');

    // Validate the name field
    if(name.value === '') {
        alert('Please enter your name.');
        return;
    }

    // Validate the email field
    var emailPattern = /^[^ ]+@[^ ]+\.[a-z]{2,3}$/;
    if(!email.value.match(emailPattern)) {
        alert('Please enter a valid email address.');
        return;
    }

    // Validate the message field
    if(message.value === '') {
        alert('Please enter your message.');
        return;
    }

    // If all fields are valid, submit the form
    alert('Your message has been sent successfully!');
    this.submit();
});
