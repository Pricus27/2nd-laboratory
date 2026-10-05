(function () {
    document.querySelectorAll("[data-toggle-password]").forEach(function (button) {
        button.addEventListener("click", function () {
            var input = document.getElementById(button.getAttribute("data-toggle-password"));
            if (!input) {
                return;
            }
            var hidden = input.type === "password";
            input.type = hidden ? "text" : "password";
            button.setAttribute("aria-label", hidden ? "Hide password" : "Show password");
        });
    });

    var socialNote = document.getElementById("social-note");
    document.querySelectorAll("[data-social]").forEach(function (button) {
        button.addEventListener("click", function () {
            if (socialNote) {
                socialNote.textContent = "Social sign-in is not available. Use your email and password.";
            }
        });
    });

    var form = document.querySelector("[data-validate]");
    if (!form) {
        return;
    }

    form.addEventListener("submit", function (event) {
        var errors = validate(form);
        clearErrors(form);
        if (Object.keys(errors).length) {
            event.preventDefault();
            showErrors(form, errors);
        }
    });

    function validate(form) {
        var mode = form.getAttribute("data-validate");
        var errors = {};
        var email = value(form, "email");
        var password = value(form, "password");

        if (mode === "login" || mode === "forgot" || mode === "register") {
            if (!email) {
                errors.email = "Email is required.";
            } else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
                errors.email = "Enter a valid email address.";
            }
        }

        if (mode === "login" || mode === "register") {
            if (!password) {
                errors.password = "Password is required.";
            }
        }

        if (mode === "register") {
            var fullname = value(form, "fullname");
            var phone = value(form, "phone");
            var confirm = value(form, "confirm_password");

            if (!fullname) {
                errors.fullname = "Full name is required.";
            } else if (fullname.length < 2 || !/^[\p{L}][\p{L}\s.'-]*$/u.test(fullname)) {
                errors.fullname = "Enter a real name using letters, spaces, hyphens, or apostrophes.";
            }

            if (!phone) {
                errors.phone = "Phone number is required.";
            } else if (!isPhone(phone)) {
                errors.phone = "Use a Philippine mobile number, such as +639123456789 or 09123456789.";
            }

            if (password && (password.length < 8 || !/[A-Za-z]/.test(password) || !/\d/.test(password))) {
                errors.password = "Use at least 8 characters with one letter and one number.";
            }

            if (!confirm) {
                errors.confirm_password = "Please confirm your password.";
            } else if (password !== confirm) {
                errors.confirm_password = "Passwords do not match.";
            }
        }

        return errors;
    }

    function isPhone(phone) {
        var compact = phone.replace(/[\s\-().]/g, "");
        return /^\+639\d{9}$/.test(compact) || /^09\d{9}$/.test(compact) || /^639\d{9}$/.test(compact);
    }

    function value(form, name) {
        var field = form.elements[name];
        return field ? String(field.value).trim() : "";
    }

    function clearErrors(form) {
        form.querySelectorAll(".field-error.js-error").forEach(function (node) {
            node.remove();
        });
        form.querySelectorAll(".js-banner").forEach(function (node) {
            node.remove();
        });
    }

    function showErrors(form, errors) {
        var banner = document.createElement("div");
        banner.className = "banner banner-error js-banner";
        banner.setAttribute("role", "alert");
        banner.textContent = "Please fix the highlighted fields.";
        var anchor = form.querySelector(".field");
        form.insertBefore(banner, anchor);

        Object.keys(errors).forEach(function (key) {
            var field = form.elements[key];
            if (!field) {
                return;
            }
            var message = document.createElement("p");
            message.className = "field-error js-error";
            message.textContent = errors[key];
            var wrap = field.closest(".field") || field.parentElement;
            wrap.appendChild(message);
        });
    }
})();
