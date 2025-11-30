const API_URL = window.location.origin;
let storyModal, impactModal, blogModal;
let currentEditingStoryId = null;
let currentEditingImpactId = null;
let currentEditingBlogId = null;

// Check authentication on page load
window.addEventListener('DOMContentLoaded', async () => {
    const token = localStorage.getItem('admin_token');
    const username = localStorage.getItem('admin_username');
    
    if (!token) {
        window.location.href = 'admin-login.html';
        return;
    }
    
    try {
        const response = await fetch(`${API_URL}/backend/api/verify.php`, {
            headers: {
                'Authorization': `Bearer ${token}`
            }
        });
        
        if (!response.ok) {
            throw new Error('Invalid token');
        }
        
        document.getElementById('adminUsername').textContent = username || 'Admin';
        
        // Initialize modals
        storyModal = new bootstrap.Modal(document.getElementById('storyModal'));
        impactModal = new bootstrap.Modal(document.getElementById('impactModal'));
        if (document.getElementById('blogModal')) {
            blogModal = new bootstrap.Modal(document.getElementById('blogModal'));
        }
        
        // Set current date
        updateCurrentDate();
        
        // Load initial data
        loadDashboardStats();
        loadStories();
        loadImpactUpdates();
        if (document.getElementById('blogsTable')) {
            loadBlogs();
        }
        
        // Setup tab navigation
        setupTabNavigation();
        
        // Setup image preview
        setupImagePreviews();
        
    } catch (error) {
        console.error('Authentication error:', error);
        localStorage.removeItem('admin_token');
        localStorage.removeItem('admin_username');
        window.location.href = 'admin-login.html';
    }
});

// Update current date
function updateCurrentDate() {
    const now = new Date();
    const options = { year: 'numeric', month: 'long', day: 'numeric' };
    document.getElementById('currentDate').textContent = now.toLocaleDateString('en-US', options);
}

// Setup tab navigation
function setupTabNavigation() {
    document.querySelectorAll('.sidebar .nav-link[data-tab]').forEach(link => {
        link.addEventListener('click', (e) => {
            e.preventDefault();
            const tabName = link.dataset.tab;
            
            // Update active link
            document.querySelectorAll('.sidebar .nav-link').forEach(l => l.classList.remove('active'));
            link.classList.add('active');
            
            // Show tab content
            document.querySelectorAll('.tab-content').forEach(tab => tab.classList.remove('active'));
            document.getElementById(tabName).classList.add('active');
        });
    });
}

// Setup image previews
function setupImagePreviews() {
    document.getElementById('storyImage').addEventListener('change', (e) => {
        previewImage(e.target, 'storyImagePreview');
    });
    
    document.getElementById('impactImage').addEventListener('change', (e) => {
        previewImage(e.target, 'impactImagePreview');
    });
    
    if (document.getElementById('blogImage')) {
        document.getElementById('blogImage').addEventListener('change', (e) => {
            previewImage(e.target, 'blogImagePreview');
        });
    }
}

function previewImage(input, previewId) {
    const preview = document.getElementById(previewId);
    
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        
        reader.onload = (e) => {
            preview.src = e.target.result;
            preview.classList.remove('d-none');
        };
        
        reader.readAsDataURL(input.files[0]);
    } else {
        preview.classList.add('d-none');
    }
}

// Logout
document.getElementById('logoutBtn').addEventListener('click', (e) => {
    e.preventDefault();
    if (confirm('Are you sure you want to logout?')) {
        localStorage.removeItem('admin_token');
        localStorage.removeItem('admin_username');
        window.location.href = 'admin-login.html';
    }
});

// Load dashboard statistics
async function loadDashboardStats() {
    const token = localStorage.getItem('admin_token');
    
    try {
        const [storiesRes, impactRes, blogsRes] = await Promise.all([
            fetch(`${API_URL}/backend/api/stories.php?published=false`, {
                headers: { 'Authorization': `Bearer ${token}` }
            }),
            fetch(`${API_URL}/backend/api/impact.php?published=false`, {
                headers: { 'Authorization': `Bearer ${token}` }
            }),
            fetch(`${API_URL}/backend/api/blog.php?published=false`, {
                headers: { 'Authorization': `Bearer ${token}` }
            }).catch(() => null)
        ]);
        
        const stories = await storiesRes.json();
        const impact = await impactRes.json();
        const blogs = blogsRes ? await blogsRes.json() : [];
        
        if (document.getElementById('totalStories')) {
            document.getElementById('totalStories').textContent = stories.length;
        }
        if (document.getElementById('totalImpact')) {
            document.getElementById('totalImpact').textContent = impact.length;
        }
        if (document.getElementById('totalBlogs')) {
            document.getElementById('totalBlogs').textContent = blogs.length;
        }
        
        // Calculate published today
        const today = new Date().toISOString().split('T')[0];
        const publishedToday = [...stories, ...impact, ...blogs].filter(item => {
            return item.created_at.startsWith(today);
        }).length;
        
        if (document.getElementById('publishedToday')) {
            document.getElementById('publishedToday').textContent = publishedToday;
        }
    } catch (error) {
        console.error('Error loading stats:', error);
    }
}

// Load stories
async function loadStories() {
    const token = localStorage.getItem('admin_token');
    const tbody = document.getElementById('storiesTable');
    
    try {
        const response = await fetch(`${API_URL}/backend/api/stories.php?published=false`, {
            headers: { 'Authorization': `Bearer ${token}` }
        });
        
        const stories = await response.json();
        
        if (stories.length === 0) {
            tbody.innerHTML = '<tr><td colspan="6" class="text-center">No stories found. Add your first story!</td></tr>';
            return;
        }
        
        tbody.innerHTML = stories.map(story => `
            <tr>
                <td>
                    ${story.image_url ? 
                        `<img src="${story.image_url}" alt="${story.title}" style="width: 80px; height: 60px; object-fit: cover; border-radius: 5px;">` :
                        '<div style="width: 80px; height: 60px; background: #f0f0f0; display: flex; align-items: center; justify-content: center; border-radius: 5px;"><i class="fas fa-image text-muted"></i></div>'
                    }
                </td>
                <td>
                    <strong>${story.title}</strong><br>
                    <small class="text-muted">${story.description.substring(0, 60)}...</small>
                </td>
                <td>${story.author}</td>
                <td>${new Date(story.created_at).toLocaleDateString()}</td>
                <td>
                    <span class="badge ${story.published ? 'badge-published' : 'badge-draft'}">
                        ${story.published ? 'Published' : 'Draft'}
                    </span>
                </td>
                <td>
                    <div class="table-actions">
                        <button class="btn btn-sm btn-info" onclick="editStory(${story.id})" title="Edit">
                            <i class="fas fa-edit"></i>
                        </button>
                        <button class="btn btn-sm btn-danger" onclick="deleteStory(${story.id})" title="Delete">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </td>
            </tr>
        `).join('');
    } catch (error) {
        console.error('Error loading stories:', error);
        tbody.innerHTML = '<tr><td colspan="6" class="text-center text-danger">Error loading stories</td></tr>';
    }
}

// Load impact updates
async function loadImpactUpdates() {
    const token = localStorage.getItem('admin_token');
    const tbody = document.getElementById('impactTable');
    
    try {
        const response = await fetch(`${API_URL}/backend/api/impact.php?published=false`, {
            headers: { 'Authorization': `Bearer ${token}` }
        });
        
        const impacts = await response.json();
        
        if (impacts.length === 0) {
            tbody.innerHTML = '<tr><td colspan="5" class="text-center">No impact updates found. Add your first update!</td></tr>';
            return;
        }
        
        tbody.innerHTML = impacts.map(impact => `
            <tr>
                <td>
                    ${impact.image_url ? 
                        `<img src="${impact.image_url}" alt="${impact.title}" style="width: 80px; height: 60px; object-fit: cover; border-radius: 5px;">` :
                        '<div style="width: 80px; height: 60px; background: #f0f0f0; display: flex; align-items: center; justify-content: center; border-radius: 5px;"><i class="fas fa-image text-muted"></i></div>'
                    }
                </td>
                <td>
                    <strong>${impact.title}</strong><br>
                    <small class="text-muted">${impact.description.substring(0, 60)}...</small>
                </td>
                <td>${new Date(impact.created_at).toLocaleDateString()}</td>
                <td>
                    <span class="badge ${impact.published ? 'badge-published' : 'badge-draft'}">
                        ${impact.published ? 'Published' : 'Draft'}
                    </span>
                </td>
                <td>
                    <div class="table-actions">
                        <button class="btn btn-sm btn-info" onclick="editImpact(${impact.id})" title="Edit">
                            <i class="fas fa-edit"></i>
                        </button>
                        <button class="btn btn-sm btn-danger" onclick="deleteImpact(${impact.id})" title="Delete">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </td>
            </tr>
        `).join('');
    } catch (error) {
        console.error('Error loading impact updates:', error);
        tbody.innerHTML = '<tr><td colspan="5" class="text-center text-danger">Error loading impact updates</td></tr>';
    }
}

// Show add story modal
function showAddStoryModal() {
    currentEditingStoryId = null;
    document.getElementById('storyModalTitle').textContent = 'Add New Story';
    document.getElementById('storyForm').reset();
    document.getElementById('storyId').value = '';
    document.getElementById('storyImagePreview').classList.add('d-none');
    document.getElementById('storyImageUrl').value = '';
    storyModal.show();
}

// Show add impact modal
function showAddImpactModal() {
    currentEditingImpactId = null;
    document.getElementById('impactModalTitle').textContent = 'Add Impact Update';
    document.getElementById('impactForm').reset();
    document.getElementById('impactId').value = '';
    document.getElementById('impactImagePreview').classList.add('d-none');
    document.getElementById('impactImageUrl').value = '';
    impactModal.show();
}

// Edit story
async function editStory(id) {
    const token = localStorage.getItem('admin_token');
    
    try {
        const response = await fetch(`${API_URL}/backend/api/stories.php?id=${id}`, {
            headers: { 'Authorization': `Bearer ${token}` }
        });
        
        const story = await response.json();
        
        currentEditingStoryId = id;
        document.getElementById('storyModalTitle').textContent = 'Edit Story';
        document.getElementById('storyId').value = story.id;
        document.getElementById('storyTitle').value = story.title;
        document.getElementById('storyDescription').value = story.description;
        document.getElementById('storyAuthor').value = story.author;
        document.getElementById('storyPublished').checked = story.published;
        document.getElementById('storyImageUrl').value = story.image_url || '';
        
        if (story.image_url) {
            document.getElementById('storyImagePreview').src = story.image_url;
            document.getElementById('storyImagePreview').classList.remove('d-none');
        }
        
        storyModal.show();
    } catch (error) {
        console.error('Error loading story:', error);
        alert('Error loading story');
    }
}

// Edit impact
async function editImpact(id) {
    const token = localStorage.getItem('admin_token');
    
    try {
        const response = await fetch(`${API_URL}/backend/api/impact.php?id=${id}`, {
            headers: { 'Authorization': `Bearer ${token}` }
        });
        
        const impact = await response.json();
        
        currentEditingImpactId = id;
        document.getElementById('impactModalTitle').textContent = 'Edit Impact Update';
        document.getElementById('impactId').value = impact.id;
        document.getElementById('impactTitle').value = impact.title;
        document.getElementById('impactDescription').value = impact.description;
        document.getElementById('impactPublished').checked = impact.published;
        document.getElementById('impactImageUrl').value = impact.image_url || '';
        
        if (impact.image_url) {
            document.getElementById('impactImagePreview').src = impact.image_url;
            document.getElementById('impactImagePreview').classList.remove('d-none');
        }
        
        impactModal.show();
    } catch (error) {
        console.error('Error loading impact update:', error);
        alert('Error loading impact update');
    }
}

// Save story
async function saveStory() {
    const token = localStorage.getItem('admin_token');
    const form = document.getElementById('storyForm');
    const storyId = document.getElementById('storyId').value;
    const formData = new FormData();
    
    formData.append('title', document.getElementById('storyTitle').value);
    formData.append('description', document.getElementById('storyDescription').value);
    formData.append('author', document.getElementById('storyAuthor').value);
    formData.append('published', document.getElementById('storyPublished').checked);
    
    const imageFile = document.getElementById('storyImage').files[0];
    if (imageFile) {
        formData.append('image', imageFile);
    } else if (document.getElementById('storyImageUrl').value) {
        formData.append('image_url', document.getElementById('storyImageUrl').value);
    }
    
    const saveBtn = document.querySelector('#storyModal .btn-primary');
    const saveText = document.getElementById('saveStoryText');
    const saveSpinner = document.getElementById('saveStorySpinner');
    
    saveBtn.disabled = true;
    saveText.classList.add('d-none');
    saveSpinner.classList.remove('d-none');
    
    try {
        let url = `${API_URL}/backend/api/stories.php`;
        let method = 'POST';
        let body = formData;
        
        if (storyId) {
            // For PHP PUT, we need to send as query parameters since FormData doesn't work with PUT
            url = `${API_URL}/backend/api/stories.php?id=${storyId}`;
            method = 'PUT';
            
            // If there's a new image, use POST with _method override
            if (imageFile) {
                method = 'POST';
                formData.append('_method', 'PUT');
            } else {
                // For PUT without file, send as URL encoded
                const params = new URLSearchParams();
                params.append('title', document.getElementById('storyTitle').value);
                params.append('description', document.getElementById('storyDescription').value);
                params.append('author', document.getElementById('storyAuthor').value);
                params.append('published', document.getElementById('storyPublished').checked);
                if (document.getElementById('storyImageUrl').value) {
                    params.append('image_url', document.getElementById('storyImageUrl').value);
                }
                body = params;
            }
        }
        
        const response = await fetch(url, {
            method: method,
            headers: {
                'Authorization': `Bearer ${token}`
            },
            body: body
        });
        
        const result = await response.json();
        
        if (response.ok) {
            alert(result.message || 'Story saved successfully!');
            storyModal.hide();
            loadStories();
            loadDashboardStats();
        } else {
            throw new Error(result.error || 'Failed to save story');
        }
    } catch (error) {
        console.error('Error saving story:', error);
        alert('Error: ' + error.message);
    } finally {
        saveBtn.disabled = false;
        saveText.classList.remove('d-none');
        saveSpinner.classList.add('d-none');
    }
}

// Save impact
async function saveImpact() {
    const token = localStorage.getItem('admin_token');
    const form = document.getElementById('impactForm');
    const impactId = document.getElementById('impactId').value;
    const formData = new FormData();
    
    formData.append('title', document.getElementById('impactTitle').value);
    formData.append('description', document.getElementById('impactDescription').value);
    formData.append('published', document.getElementById('impactPublished').checked);
    
    const imageFile = document.getElementById('impactImage').files[0];
    if (imageFile) {
        formData.append('image', imageFile);
    } else if (document.getElementById('impactImageUrl').value) {
        formData.append('image_url', document.getElementById('impactImageUrl').value);
    }
    
    const saveBtn = document.querySelector('#impactModal .btn-primary');
    const saveText = document.getElementById('saveImpactText');
    const saveSpinner = document.getElementById('saveImpactSpinner');
    
    saveBtn.disabled = true;
    saveText.classList.add('d-none');
    saveSpinner.classList.remove('d-none');
    
    try {
        let url = `${API_URL}/backend/api/impact.php`;
        let method = 'POST';
        let body = formData;
        
        if (impactId) {
            // For PHP PUT, we need to send as query parameters since FormData doesn't work with PUT
            url = `${API_URL}/backend/api/impact.php?id=${impactId}`;
            method = 'PUT';
            
            // If there's a new image, use POST with _method override
            if (imageFile) {
                method = 'POST';
                formData.append('_method', 'PUT');
            } else {
                // For PUT without file, send as URL encoded
                const params = new URLSearchParams();
                params.append('title', document.getElementById('impactTitle').value);
                params.append('description', document.getElementById('impactDescription').value);
                params.append('published', document.getElementById('impactPublished').checked);
                if (document.getElementById('impactImageUrl').value) {
                    params.append('image_url', document.getElementById('impactImageUrl').value);
                }
                body = params;
            }
        }
        
        const response = await fetch(url, {
            method: method,
            headers: {
                'Authorization': `Bearer ${token}`
            },
            body: body
        });
        
        const result = await response.json();
        
        if (response.ok) {
            alert(result.message || 'Impact update saved successfully!');
            impactModal.hide();
            loadImpactUpdates();
            loadDashboardStats();
        } else {
            throw new Error(result.error || 'Failed to save impact update');
        }
    } catch (error) {
        console.error('Error saving impact update:', error);
        alert('Error: ' + error.message);
    } finally {
        saveBtn.disabled = false;
        saveText.classList.remove('d-none');
        saveSpinner.classList.add('d-none');
    }
}

// Delete story
async function deleteStory(id) {
    if (!confirm('Are you sure you want to delete this story?')) {
        return;
    }
    
    const token = localStorage.getItem('admin_token');
    
    try {
        const response = await fetch(`${API_URL}/backend/api/stories.php?id=${id}`, {
            method: 'DELETE',
            headers: {
                'Authorization': `Bearer ${token}`
            }
        });
        
        const result = await response.json();
        
        if (response.ok) {
            alert(result.message || 'Story deleted successfully!');
            loadStories();
            loadDashboardStats();
        } else {
            throw new Error(result.error || 'Failed to delete story');
        }
    } catch (error) {
        console.error('Error deleting story:', error);
        alert('Error: ' + error.message);
    }
}

// Delete impact
async function deleteImpact(id) {
    if (!confirm('Are you sure you want to delete this impact update?')) {
        return;
    }
    
    const token = localStorage.getItem('admin_token');
    
    try {
        const response = await fetch(`${API_URL}/backend/api/impact.php?id=${id}`, {
            method: 'DELETE',
            headers: {
                'Authorization': `Bearer ${token}`
            }
        });
        
        const result = await response.json();
        
        if (response.ok) {
            alert(result.message || 'Impact update deleted successfully!');
            loadImpactUpdates();
            loadDashboardStats();
        } else {
            throw new Error(result.error || 'Failed to delete impact update');
        }
    } catch (error) {
        console.error('Error deleting impact update:', error);
        alert('Error: ' + error.message);
    }
}

