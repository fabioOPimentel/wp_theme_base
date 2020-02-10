var formTab = {
    data: function () {
        return {
            currentTab: 0
        }
    },
    methods: {
        showTab: function (n) {
            var self = this;
            // This function will display the specified tab of the form ...
            var x = document.getElementsByClassName("tab");
            if(x.length !== 0){
                x[n].style.display = "flex";
                // ... and fix the Previous/Next buttons:
                if (n == 0) {
                    document.getElementById("prevBtn").style.display = "none";
                } else {
                    document.getElementById("prevBtn").style.display = "block";
                }
                if (n == (x.length - 1)) {
                    document.getElementById("sendBtn").style.display = "block";
                    document.getElementById("nextBtn").style.display = "none";
                } else {
                    document.getElementById("sendBtn").style.display = "none";
                    document.getElementById("nextBtn").style.display = "block";
                }
                // ... and run a function that displays the correct step indicator:
                self.fixStepIndicator(n)
            }
        },
        nextPrev: function (n) {
            var self = this;
            // This function will figure out which tab to display
            var x = document.getElementsByClassName("tab");
            // Exit the function if any field in the current tab is invalid:
            if (n == 1 && !self.validateForm()) return false;
            // Hide the current tab:
            x[self.currentTab].style.display = "none";
            // Increase or decrease the current tab by 1:
            self.currentTab = self.currentTab + n;
            // Otherwise, display the correct tab:
            self.showTab(self.currentTab);
        },
        validateForm: function () {
            var self = this;
            // This function deals with validation of the form fields
            var x, y, i, valid = true;
            x = document.getElementsByClassName("tab");
            y = x[self.currentTab].getElementsByClassName("form-control");
            // A loop that checks every input field in the current tab:
            for (i = 0; i < y.length; i++) {
                    // If a field is empty...
                if (y[i].checkValidity() == false) {
                    // add an "invalid" class to the field:
                    y[i].className += " is-invalid";
                    // and set the current valid status to false:
                    valid = false;
                }
            }
            // If the valid status is true, mark the step as finished and valid:
            if (valid) {
                document.getElementsByClassName("step")[self.currentTab].className += " finish";
            }
            return valid; // return the valid status
        },
        fixStepIndicator: function (n) {
            //This function removes the "active" class of all steps...
            var i, x = document.getElementsByClassName("step");
            for (i = 0; i < x.length; i++) {
                x[i].className = x[i].className.replace(" active", "");
            }
            //... and adds the "active" class to the current step:
            x[n].className += " active";
        }
    }
}