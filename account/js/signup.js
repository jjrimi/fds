document.addEventListener("DOMContentLoaded", function() {
    const idCheckResult = document.getElementById("idCheckResult");
    const passwordError = document.getElementById("passwordError");
    const confirmPasswordError = document.getElementById("confirmPasswordError");
    const emailError = document.getElementById("emailError");
    const fullNameError = document.getElementById("full_nameError");
    const phoneError = document.getElementById("phonenumberError");
    const birthdateError = document.getElementById("date_of_birthError");
    const checkIdButton = document.getElementById("checkId");
    const signupForm = document.getElementById("signupForm");

    checkIdButton.addEventListener("click", function() {
        const userid = document.getElementById("username").value;
        if (!userid) {
            idCheckResult.innerText = "ID를 입력해주세요.";
            return;
        }

        // AJAX를 이용한 ID 중복 검사
        const xhr = new XMLHttpRequest();
        xhr.open("POST", "signup.php", true);
        xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
        xhr.onload = function() {
            if (this.status == 200) {
                const response = JSON.parse(this.responseText);
                if(response.duplicate) {
                    idCheckResult.innerText = "이미 사용중인 ID입니다.";
                    idCheckResult.className = "error";
                } else {
                    idCheckResult.innerText = "사용 가능한 ID입니다.";
                    idCheckResult.className = "success";
                }
            }
        }
        xhr.send("checkId=true&username=" + encodeURIComponent(userid));
    });

    // 성별 선택
    document.querySelectorAll(".dropdown-item").forEach(item => {
        item.addEventListener("click", function() {
            document.getElementById("genderInput").value = this.innerText;
        });
    });

    // 회원가입 폼 제출 시 중복 검사
    signupForm.addEventListener("submit", function(event) {
        event.preventDefault();

        const email = document.getElementById("email").value;
        const phone = document.getElementById("phonenumber").value;

        const xhr = new XMLHttpRequest();
        xhr.open("POST", "signup.php", true);
        xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
        xhr.onload = function() {
            if (this.status == 200) {
                const response = JSON.parse(this.responseText);
                if (response.emailDuplicate) {
                    emailError.innerText = "이미 등록된 이메일입니다.";
                    emailError.className = "error";
                } else {
                    emailError.innerText = "";
                    emailError.className = "";
                }

                if (response.phoneDuplicate) {
                    phoneError.innerText = "이미 등록된 전화번호입니다.";
                    phoneError.className = "error";
                } else {
                    phoneError.innerText = "";
                    phoneError.className = "";
                }

                if (!response.emailDuplicate && !response.phoneDuplicate) {
                    signupForm.submit();
                }
            }
        }
        xhr.send("checkSignup=true&email=" + encodeURIComponent(email) + "&phone=" + encodeURIComponent(phone));
    });
});
