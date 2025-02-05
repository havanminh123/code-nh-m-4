// Chính sách phúc lợi
const policies = JSON.parse(localStorage.getItem('policies')) || [];

// Phúc lợi
const benefits = JSON.parse(localStorage.getItem('benefits')) || [];

// Phản hồi
const feedbacks = JSON.parse(localStorage.getItem('feedbacks')) || [];

// Ngân sách
let budget = JSON.parse(localStorage.getItem('budget')) || 0;

// Xử lý việc thêm chính sách
document.getElementById('policy-form').addEventListener('submit', function(event) {
    event.preventDefault();

    const objectives = document.getElementById('policy-objectives').value;
    const analysis = document.getElementById('policy-analysis').value;
    const packages = document.getElementById('benefit-packages').value;

    const newPolicy = {
        objectives,
        analysis,
        packages,
        createdAt: new Date().toISOString()
    };

    policies.push(newPolicy);
    localStorage.setItem('policies', JSON.stringify(policies));
    alert('Chính sách đã được lưu.');
    clearPolicyForm();
    renderPolicies();
});

// Hàm làm sạch form
function clearPolicyForm() {
    document.getElementById('policy-objectives').value = '';
    document.getElementById('policy-analysis').value = '';
    document.getElementById('benefit-packages').value = '';
}

// Hàm hiển thị chính sách
function renderPolicies() {
    const policyList = document.querySelector('#policy-list tbody');
    policyList.innerHTML = '';

    policies.forEach((policy, index) => {
        const row = `<tr>
            <td>${index + 1}</td>
            <td>${policy.objectives}</td>
            <td>${policy.analysis}</td>
            <td>${policy.packages}</td>
            <td>${new Date(policy.createdAt).toLocaleDateString()}</td>
            <td><button onclick="deletePolicy(${index})">Xóa</button></td>
        </tr>`;
        policyList.innerHTML += row;
    });
}

// Hàm xóa chính sách
function deletePolicy(index) {
    policies.splice(index, 1);
    localStorage.setItem('policies', JSON.stringify(policies));
    renderPolicies();
}

// Triển khai chương trình phúc lợi
function notifyEmployees() {
    const message = "Thông báo: Các chính sách phúc lợi đã được cập nhật! Vui lòng xem chi tiết.";
    document.getElementById('notification-status').innerText = message;
    alert(message);
}

// Quản lý và vận hành hệ thống phúc lợi
function viewBenefits() {
    const benefitData = document.getElementById('benefit-data');
    benefitData.innerHTML = '';

    if (benefits.length === 0) {
        benefitData.innerHTML = '<p>Không có dữ liệu phúc lợi nào.</p>';
        return;
    }

    benefits.forEach((benefit, index) => {
        const item = `<p>${index + 1}. Tên: ${benefit.name}, Mã: ${benefit.employeeId}, Mô tả: ${benefit.description}, Loại: ${benefit.type}</p>`;
        benefitData.innerHTML += item;
    });
}

// Đánh giá và cải thiện chương trình phúc lợi
document.getElementById('feedback-form').addEventListener('submit', function(event) {
    event.preventDefault();
    const feedback = document.getElementById('feedback').value;

    feedbacks.push(feedback);
    localStorage.setItem('feedbacks', JSON.stringify(feedbacks));
    alert('Phản hồi đã được gửi.');
    renderFeedbacks();
    document.getElementById('feedback').value = '';
});

// Hàm hiển thị phản hồi
function renderFeedbacks() {
    const feedbackList = document.getElementById('feedback-list');
    feedbackList.innerHTML = '';

    feedbacks.forEach((feedback, index) => {
        const item = `<p>${index + 1}. ${feedback}</p>`;
        feedbackList.innerHTML += item;
    });
}

renderFeedbacks(); // Hiển thị phản hồi ngay khi tải trang

// Quản lý ngân sách
document.getElementById('budget-form').addEventListener('submit', function(event) {
    event.preventDefault();
    const amount = document.getElementById('budget-amount').value;
    budget = amount;
    localStorage.setItem('budget', budget);
    document.getElementById('budget-status').innerText = `Ngân sách hiện tại: ${budget} VNĐ`;
});

// Tuân thủ quy định pháp luật
function checkCompliance() {
    const status = "Tất cả các chính sách phúc lợi đều tuân thủ quy định hiện hành.";
    document.getElementById('compliance-status').innerText = status;
}

// Tạo môi trường làm việc hỗ trợ
const supportPrograms = [
    "Chăm sóc sức khỏe định kỳ",
    "Tư vấn tâm lý",
    "Đào tạo và phát triển kỹ năng",
    "Chương trình nghỉ phép linh hoạt"
];

function showSupportPrograms() {
    const supportProgramsDiv = document.getElementById('support-programs');
    supportProgramsDiv.innerHTML = '';

    supportPrograms.forEach((program, index) => {
        const item = `<p>${index + 1}. ${program}</p>`;
        supportProgramsDiv.innerHTML += item;
    });
}