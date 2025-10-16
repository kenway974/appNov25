document.addEventListener('DOMContentLoaded', () => {
    const isRecurringCheckbox = document.querySelector('#user_action_isRecurring');
    const recurrentFields = document.getElementById('recurrent-fields');
    const punctualFields = document.getElementById('punctual-fields');

    if (!isRecurringCheckbox) return;

    function toggleFields() {
        if (isRecurringCheckbox.checked) {
            recurrentFields.style.display = 'block';
            punctualFields.style.display = 'none';
        } else {
            recurrentFields.style.display = 'none';
            punctualFields.style.display = 'block';
        }
    }

    // Initial toggle à la charge de la page
    toggleFields();

    // Toggle quand on clique sur la checkbox
    isRecurringCheckbox.addEventListener('change', toggleFields);
});
