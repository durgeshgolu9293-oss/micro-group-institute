// Micro Group Admin JavaScript - Dynamic Fee Calculator & Helpers
function calculateFinalFee() {
    const feeInput = document.getElementById('course_fee');
    const adminFeeInput = document.getElementById('admission_fee');
    const discountInput = document.getElementById('discount');
    const finalFeeInput = document.getElementById('final_fee');

    if (feeInput && finalFeeInput) {
        const fee = parseFloat(feeInput.value) || 0;
        const admission = parseFloat(adminFeeInput ? adminFeeInput.value : 0) || 0;
        const discount = parseFloat(discountInput ? discountInput.value : 0) || 0;
        const final = Math.max(0, fee + admission - discount);
        finalFeeInput.value = final.toFixed(2);
    }
}

document.addEventListener('DOMContentLoaded', () => {
    const feeElements = ['course_fee', 'admission_fee', 'discount'];
    feeElements.forEach(id => {
        const el = document.getElementById(id);
        if (el) {
            el.addEventListener('input', calculateFinalFee);
        }
    });
});