document.addEventListener("DOMContentLoaded", function() {
    // Elements for adding attributes
    const addAttributeButton = document.getElementById("addAttributeButton");
    const addAttributeModal = document.getElementById("addAttributeModal");
    const closeAddModalBtns = document.querySelectorAll("#closeModal, #closeModalBtn");
    const successMessage = document.getElementById("successMessage");
    const closeMsg = document.getElementById("closeMsg");
    const attributeForm = document.getElementById("attributeForm");
    const attributeValueInput = document.getElementById("attributeValue");
    const attributeValueError = document.getElementById("attributeValueError");

    // Elements for editing attributes
    const editAttributeModal = document.getElementById("editAttributeModal");
    const editCloseModalBtn = document.getElementById("editCloseModalBtn");
    const editAttributeForm = document.getElementById("editAttributeForm");
    const editAttributeValueInput = document.getElementById("editAttributeValue");
    const editAttributeValueError = document.getElementById("editAttributeValueError");

    // Elements for deleting attributes
    const deleteAttributeModal = document.getElementById("deleteAttributeModal");
    const closeDeleteModalBtn = document.getElementById("closeDeleteModal");
    const cancelDeleteBtn = document.getElementById("cancelDeleteBtn");
    const confirmDeleteBtn = document.getElementById("confirmDeleteBtn");

    let currentAttributeId = null;
    let currentValueId = null;

    // Open delete attribute modal
    function openDeleteModal(attributeId, valueId, value) {
        currentAttributeId = attributeId;
        currentValueId = valueId;
        deleteAttributeModal.style.display = "flex";
    }

    // Close delete attribute modal
    function closeDeleteModal() {
        deleteAttributeModal.style.display = "none";
        currentAttributeId = null;
        currentValueId = null;
    }

    closeDeleteModalBtn.addEventListener("click", closeDeleteModal);
    cancelDeleteBtn.addEventListener("click", closeDeleteModal);

    document.querySelectorAll('.delete-btn').forEach(button => {
        button.addEventListener('click', function() {
            const attributeId = this.getAttribute('data-attribute');
            const valueId = this.getAttribute('data-id');
            const value = this.getAttribute('data-value');
            openDeleteModal(attributeId, valueId, value);
        });
    });

    // Confirm delete attribute
    confirmDeleteBtn.addEventListener("click", async function() {
        if (currentAttributeId && currentValueId) {
            const formData = new FormData();
            formData.append('attributeId', currentAttributeId);
            formData.append('valueId', currentValueId);
    
            try {
                const response = await fetch('attributes_value.php?', { 
                    method: 'POST', 
                    body: formData 
                });
                const data = await response.json();
    
                if (data.success) {
                    console.log('Server Response:', data);
                    closeDeleteModal();
                    successMessage.style.display = "block";
                    setTimeout(() => window.location.reload(), 2000);
                } else {
                    throw new Error(data.message || "Failed to delete attribute.");
                }
            } catch (error) {
                alert(error.message);
                closeDeleteModal();
            }
        }
    });

    // Open add attribute modal
    addAttributeButton.addEventListener("click", () => {
        addAttributeModal.style.display = "flex";
    });

    // Close add attribute modal
    closeAddModalBtns.forEach(btn => {
        btn.addEventListener("click", () => {
            addAttributeModal.style.display = "none";
            attributeForm.reset();
        });
    });

    // Close success message
    closeMsg.addEventListener("click", () => {
        successMessage.style.display = "none";
    });

    // Add attribute form submission
    attributeForm.addEventListener("submit", async function(event) {
        event.preventDefault();

        if (!attributeValueInput.value.trim()) {
            attributeValueError.textContent = "Please enter an attribute value.";
            attributeValueError.style.display = "block";
            return;
        }

        const formData = new FormData(this);
        const url = `${this.action}?attribute_id=${formData.get('attributeId')}`;

        try {
            const response = await fetch(url, { method: 'POST', body: formData });
            const data = await response.json();

            if (data.success) {
                addAttributeModal.style.display = "none";
                successMessage.style.display = "block";
                setTimeout(() => window.location.reload(), 2000);
            } else {
                throw new Error(data.message || "Failed to add attribute.");
            }
        } catch (error) {
            attributeValueError.textContent = error.message;
            attributeValueError.style.display = "block";
        }
    });

    // Open edit attribute modal
    function openEditModal(attributeId, valueId, attributeValue) {
        currentAttributeId = attributeId;
        currentValueId = valueId;
        editAttributeValueInput.value = attributeValue;
        editAttributeModal.style.display = "flex";
    }

    // Close edit attribute modal
    editCloseModalBtn.addEventListener("click", () => {
        editAttributeModal.style.display = "none";
        editAttributeForm.reset();
    });

    // Attach event listeners to edit and delete buttons
    document.querySelectorAll('.edit-btn').forEach(button => {
        button.addEventListener('click', function() {
            const attributeId = this.getAttribute('data-attribute-id');
            const valueId = this.getAttribute('data-value-id');
            const attributeValue = this.getAttribute('data-attribute-value');
            openEditModal(attributeId, valueId, attributeValue);
        });
    });

    // Edit attribute form submission
    editAttributeForm.addEventListener("submit", async function(event) {
        event.preventDefault();

        if (!editAttributeValueInput.value.trim()) {
            editAttributeValueError.textContent = "Please enter an attribute value.";
            editAttributeValueError.style.display = "block";
            return;
        }

        const formData = new FormData();
        formData.append('attributeId', currentAttributeId);
        formData.append('valueId', currentValueId);
        formData.append('value', editAttributeValueInput.value);

        const url = `${this.action}?attribute_id=${currentAttributeId}`;

        try {
            const response = await fetch(url, { method: 'POST', body: formData });
            const data = await response.json();

            if (data.success) {
                editAttributeModal.style.display = "none";
                successMessage.style.display = "block";
                setTimeout(() => window.location.reload(), 2000);
            } else {
                throw new Error(data.message || "Failed to update attribute.");
            }
        } catch (error) {
            editAttributeValueError.textContent = error.message;
            editAttributeValueError.style.display = "block";
        }
    });
});