function updatePrice(element) {
    // Get the closest row and its relevant elements
    const row = element.closest('tr');
    const select = row.querySelector('select[name="product[]"]');
    const quantityInput = row.querySelector('input[name="quantity[]"]');
    const priceInput = row.querySelector('input[name="price[]"]');
    
    const selectedOption = select.options[select.selectedIndex];
    
    // Ensure valid selection
    if (selectedOption.value !== "") {
        // Parse product price and quantity
        const productPrice = parseFloat(selectedOption.dataset.price) || 0;
        const quantity = parseInt(quantityInput.value) || 1;
        
        // Update price field
        priceInput.value = (productPrice * quantity).toFixed(2);
    } else {
        // Clear price if no product is selected
        priceInput.value = "";
    }

    // Update total amounts
    updateTotals();
}

function updateTotals() {
    const priceInputs = document.getElementsByName('price[]');
    const feeInputs = document.getElementsByName('fee[]');
    let grossAmount = 0;

    // Loop through all price and fee inputs
    for (let i = 0; i < priceInputs.length; i++) {
        const price = parseFloat(priceInputs[i].value) || 0;
        const fee = parseFloat(feeInputs[i].value) || 0;
        grossAmount += price + fee;
    }

    // Fetch VAT percentage from a global variable or a data attribute
    const vatPercentage = parseFloat(document.body.dataset.vatPercentage || 0);
    const vatAmount = grossAmount * (vatPercentage / 100);
    
    // Fetch discount
    const discount = parseFloat(document.querySelector('input[name="discount"]').value) || 0;

    // Calculate net amount
    const netAmount = grossAmount + vatAmount - discount;

    // Update the displayed totals
    document.getElementById('gross-amount').textContent = grossAmount.toFixed(2);
    document.getElementById('vat-amount').textContent = vatAmount.toFixed(2);
    document.getElementById('net-amount').textContent = netAmount.toFixed(2);

    // Update hidden input fields for form submission
    document.getElementById('gross-amount-input').value = grossAmount.toFixed(2);
    document.getElementById('vat-amount-input').value = vatAmount.toFixed(2);
    document.getElementById('net-amount-input').value = netAmount.toFixed(2);
}
