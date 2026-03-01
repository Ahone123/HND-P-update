// Sidebar Toggle
document.getElementById("menuToggle")?.addEventListener("click", function() {
    document.querySelector(".sidebar")?.classList.toggle("active");
});

// Image Preview
const wasteImage = document.getElementById("wasteImage");
if(wasteImage){
    wasteImage.addEventListener("change", function(event){
        const reader = new FileReader();
        reader.onload = function(){
            const prev = document.getElementById("preview");
            prev.src = reader.result;
            prev.style.display = 'block';
        }
        reader.readAsDataURL(event.target.files[0]);
    });
}

// Sidebar Toggle
function initSidebar() {
    const toggle = document.getElementById("menuToggle");
    const sidebar = document.querySelector(".sidebar");
    if (toggle && sidebar) {
        toggle.addEventListener("click", function () {
            sidebar.classList.toggle("active");
        });
    }
}

// count-up animation for dashboard cards
function animateCounters() {
    const counters = document.querySelectorAll('.stats .card h2');
    counters.forEach(counter => {
        const update = () => {
            const target = +counter.getAttribute('data-target');
            const current = +counter.innerText;
            const increment = Math.ceil(target / 100);
            if (current < target) {
                counter.innerText = current + increment;
                setTimeout(update, 20);
            } else {
                counter.innerText = target;
            }
        };
        update();
    });
}

// (second image preview handler removed; logic already applied earlier)

// Assign Driver
function assignDriver(){
    let driver = document.getElementById("driverSelect").value;
    alert("Driver " + driver + " assigned successfully!");
}

// Payment
function makePayment(){
    if(confirm("Confirm Payment?")){
        alert("Payment Successful!");
    } else {
        alert("Payment Cancelled");
    }
}

// Register
document.getElementById('formy')?.addEventListener('submit', function(event) {
    event.preventDefault();
    const name = document.getElementById('name').value.trim();
    const email = document.getElementById('email').value.trim();
    const password = document.getElementById('password').value.trim();
    const errorMsg = document.getElementById('error');
    const namerror = document.getElementById('namerror');
    const emailerror = document.getElementById('emailerror');
    const passworderror = document.getElementById('passworderror');

    if(!/^\d{10}$/.test(password)) {
        passworderror.textContent = 'password must be exactly 10 digits'
        return false;
    }
    if (name === '' || email === '' || password === '') {
        errorMsg.textContent = 'all inputs must be filled'
        return false;
    }

    const emailRegex= new RegExp("^[^\\s@]+@[^\\s@]+\\.[^\\s@]+$"); //double backslash needed
    if(!emailRegex.test(email)){
        emailerror.textContent = 'please enter a valid email';
        return false;
    }
    
    alert();
    return true;
});

// initialize on DOM load
document.addEventListener('DOMContentLoaded', function(){
    initSidebar();
    animateCounters();
});
