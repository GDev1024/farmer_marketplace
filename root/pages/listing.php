<main class="page-main listing-page" id="main-content" role="main">
    <header class="page-header">
        <h1 class="page-title">List Your Produce</h1>
        <p class="page-subtitle">Share your fresh produce with the community and connect with local buyers</p>
    </header>

    <section class="listing-form-section" aria-labelledby="listing-form-title">
        <div class="card">
            <header class="card-header">
                <h2 id="listing-form-title" class="listing-section-title">
                    <span class="section-icon" aria-hidden="true">🌾</span>
                    Product Information
                </h2>
                <p class="section-subtitle">Provide details about your fresh produce</p>
            </header>
            
            <div class="card-body">
                <form method="POST" action="actions.php" enctype="multipart/form-data" class="listing-form" aria-label="Product listing form">
                    <div class="form-group">
                        <label for="productName" class="form-label">Product Name</label>
                        <input type="text" 
                               id="productName" 
                               name="productName" 
                               class="form-input"
                               placeholder="e.g., Fresh Organic Tomatoes" 
                               required
                               aria-describedby="productName-help">
                        <small id="productName-help" class="form-help">
                            Choose a clear, descriptive name for your produce
                        </small>
                    </div>
                    
                    <div class="form-group">
                        <label for="category" class="form-label">Category</label>
                        <select id="category" name="category" class="form-input" required aria-describedby="category-help">
                            <option value="">Select a category</option>
                            <option value="vegetables">🥬 Vegetables</option>
                            <option value="fruits">🍎 Fruits</option>
                            <option value="herbs">🌿 Herbs & Spices</option>
                            <option value="grains">🌾 Grains & Legumes</option>
                        </select>
                        <small id="category-help" class="form-help">
                            Choose the category that best describes your produce
                        </small>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label for="price" class="form-label">Price (EC$)</label>
                            <input type="number" 
                                   id="price" 
                                   name="price" 
                                   class="form-input"
                                   step="0.01" 
                                   min="0" 
                                   placeholder="0.00" 
                                   required
                                   aria-describedby="price-help">
                            <small id="price-help" class="form-help">
                                Set a competitive price per unit
                            </small>
                        </div>
                        <div class="form-group">
                            <label for="unit" class="form-label">Unit Type</label>
                            <select id="unit" name="unit" class="form-input" required aria-describedby="unit-help">
                                <option value="">Select unit</option>
                                <option value="lbs">Pounds (lbs)</option>
                                <option value="kg">Kilograms (kg)</option>
                                <option value="pieces">Pieces</option>
                                <option value="bunches">Bunches</option>
                            </select>
                            <small id="unit-help" class="form-help">
                                How do you sell this produce?
                            </small>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="quantity" class="form-label">Quantity Available</label>
                        <input type="number" 
                               id="quantity" 
                               name="quantity" 
                               class="form-input"
                               min="0" 
                               placeholder="How many units do you have?" 
                               required
                               aria-describedby="quantity-help">
                        <small id="quantity-help" class="form-help">
                            Enter the total quantity you have available for sale
                        </small>
                    </div>
                    
                    <div class="form-group">
                        <label for="description" class="form-label">Description</label>
                        <textarea id="description" 
                                  name="description" 
                                  class="form-input description-textarea"
                                  placeholder="Describe your produce - freshness, growing methods, harvest date, etc." 
                                  required
                                  aria-describedby="description-help"></textarea>
                        <small id="description-help" class="form-help">
                            Provide detailed information to help buyers make informed decisions
                        </small>
                    </div>
                    
                    <div class="form-group">
                        <label for="productImage" class="form-label">Product Photo <span class="optional-label">(Optional)</span></label>
                        <div class="image-upload-wrapper">
                            <input type="file" 
                                   name="productImage" 
                                   accept="image/jpeg,image/jpg,image/png" 
                                   id="productImage" 
                                   class="image-upload-input"
                                   aria-describedby="image-help">
                            <div class="image-upload-content">
                                <div class="upload-icon" aria-hidden="true">📸</div>
                                <div class="upload-text">
                                    <span class="upload-primary">Click to upload or drag and drop</span>
                                    <span class="upload-secondary">JPG or PNG, max 5MB</span>
                                </div>
                            </div>
                        </div>
                        <small id="image-help" class="form-help">
                            High-quality images help attract more buyers and increase sales
                        </small>
                        
                        <div id="imagePreview" class="image-preview" style="display: none;" aria-live="polite">
                            <img id="previewImg" class="preview-image" alt="Product image preview">
                            <p class="preview-caption">Preview of your uploaded image</p>
                            <button type="button" class="btn btn-sm btn-secondary remove-image-btn" onclick="removeImage()">
                                <span class="btn-icon" aria-hidden="true">🗑️</span>
                                Remove Image
                            </button>
                        </div>
                    </div>
                    
                    <div class="listing-tips">
                        <h3 class="tips-title">
                            <span class="tips-icon" aria-hidden="true">💡</span>
                            Tips for Better Sales
                        </h3>
                        <ul class="tips-list">
                            <li>Use clear, well-lit photos of your produce</li>
                            <li>Mention if your produce is organic or pesticide-free</li>
                            <li>Include harvest date or freshness information</li>
                            <li>Set competitive prices based on quality</li>
                            <li>Respond quickly to buyer inquiries</li>
                        </ul>
                    </div>
                    
                    <div class="form-actions">
                        <button type="submit" name="listProduct" class="btn btn-primary btn-lg submit-btn">
                            <span class="btn-icon" aria-hidden="true">✨</span>
                            List My Produce
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </section>
</main>

<script>
// Enhanced image upload functionality
function initializeImageUpload() {
    const fileInput = document.getElementById('productImage');
    const uploadWrapper = document.querySelector('.image-upload-wrapper');
    const preview = document.getElementById('imagePreview');
    const previewImg = document.getElementById('previewImg');
    
    if (!fileInput || !uploadWrapper || !preview || !previewImg) return;
    
    // File input change handler
    fileInput.addEventListener('change', function(e) {
        handleFileSelection(e.target.files[0]);
    });
    
    // Drag and drop handlers
    uploadWrapper.addEventListener('dragover', function(e) {
        e.preventDefault();
        uploadWrapper.classList.add('drag-over');
    });
    
    uploadWrapper.addEventListener('dragleave', function(e) {
        e.preventDefault();
        uploadWrapper.classList.remove('drag-over');
    });
    
    uploadWrapper.addEventListener('drop', function(e) {
        e.preventDefault();
        uploadWrapper.classList.remove('drag-over');
        const files = e.dataTransfer.files;
        if (files.length > 0) {
            fileInput.files = files;
            handleFileSelection(files[0]);
        }
    });
    
    // Click handler for upload area
    uploadWrapper.addEventListener('click', function() {
        fileInput.click();
    });
}

function handleFileSelection(file) {
    const uploadWrapper = document.querySelector('.image-upload-wrapper');
    const preview = document.getElementById('imagePreview');
    const previewImg = document.getElementById('previewImg');
    
    if (!file) {
        resetImageUpload();
        return;
    }
    
    // Validate file type
    if (!file.type.match(/^image\/(jpeg|jpg|png)$/)) {
        showImageError('Please select a JPG or PNG image file.');
        return;
    }
    
    // Validate file size (5MB)
    if (file.size > 5 * 1024 * 1024) {
        showImageError('Image file is too large. Please select a file smaller than 5MB.');
        return;
    }
    
    // Show preview
    const reader = new FileReader();
    reader.onload = function(e) {
        previewImg.src = e.target.result;
        previewImg.alt = `Preview of ${file.name}`;
        preview.style.display = 'block';
        uploadWrapper.classList.add('has-file');
        
        // Announce to screen readers
        const announcement = document.createElement('div');
        announcement.setAttribute('aria-live', 'polite');
        announcement.setAttribute('aria-atomic', 'true');
        announcement.className = 'sr-only';
        announcement.textContent = `Image ${file.name} uploaded successfully`;
        document.body.appendChild(announcement);
        setTimeout(() => document.body.removeChild(announcement), 1000);
    };
    reader.readAsDataURL(file);
}

function showImageError(message) {
    const fileInput = document.getElementById('productImage');
    const uploadWrapper = document.querySelector('.image-upload-wrapper');
    
    alert(message);
    fileInput.value = '';
    uploadWrapper.classList.add('error');
    uploadWrapper.classList.remove('has-file');
    
    setTimeout(() => {
        uploadWrapper.classList.remove('error');
    }, 3000);
    
    resetImageUpload();
}

function removeImage() {
    const fileInput = document.getElementById('productImage');
    fileInput.value = '';
    resetImageUpload();
    
    // Announce to screen readers
    const announcement = document.createElement('div');
    announcement.setAttribute('aria-live', 'polite');
    announcement.setAttribute('aria-atomic', 'true');
    announcement.className = 'sr-only';
    announcement.textContent = 'Image removed';
    document.body.appendChild(announcement);
    setTimeout(() => document.body.removeChild(announcement), 1000);
}

function resetImageUpload() {
    const uploadWrapper = document.querySelector('.image-upload-wrapper');
    const preview = document.getElementById('imagePreview');
    
    if (uploadWrapper) {
        uploadWrapper.classList.remove('has-file', 'error', 'drag-over');
    }
    
    if (preview) {
        preview.style.display = 'none';
    }
}

// Form validation enhancement
function initializeFormValidation() {
    const form = document.querySelector('.listing-form');
    if (!form) return;
    
    form.addEventListener('submit', function(e) {
        const requiredFields = form.querySelectorAll('[required]');
        let isValid = true;
        
        requiredFields.forEach(field => {
            if (!field.value.trim()) {
                field.classList.add('error');
                isValid = false;
            } else {
                field.classList.remove('error');
            }
        });
        
        if (!isValid) {
            e.preventDefault();
            const firstError = form.querySelector('.error');
            if (firstError) {
                firstError.focus();
                firstError.scrollIntoView({ behavior: 'smooth', block: 'center' });
            }
        }
    });
    
    // Real-time validation
    const inputs = form.querySelectorAll('input, select, textarea');
    inputs.forEach(input => {
        input.addEventListener('blur', function() {
            if (this.hasAttribute('required') && !this.value.trim()) {
                this.classList.add('error');
            } else {
                this.classList.remove('error');
            }
        });
        
        input.addEventListener('input', function() {
            if (this.classList.contains('error') && this.value.trim()) {
                this.classList.remove('error');
            }
        });
    });
}

// Initialize when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    initializeImageUpload();
    initializeFormValidation();
});
</script>
