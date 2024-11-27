document.addEventListener('DOMContentLoaded', function() {
    // Get CSRF token from meta tag
    const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
    
    // Add event listeners for view buttons
    document.querySelectorAll('.btn-view').forEach(button => {
        button.addEventListener('click', function() {
            viewDetails(this.dataset.logId, this.dataset.logType); // logType will be either 'user' or 'admin'
        });
    });

    // Add event listeners for block IP buttons
    document.querySelectorAll('.btn-block-ip').forEach(button => {
        button.addEventListener('click', function() {
            const ip = this.dataset.ip;
            if (confirm(`Are you sure you want to block this IP address: ${ip}?`)) {
                blockIP(ip);
            }
        });
    });
});

/**
 * View detailed log information
 * @param {string} logId - The ID of the log entry
 * @param {string} logType - Type of log ('user' or 'admin')
 */
async function viewDetails(logId, logType) {
    if (!logId) return;

    try {
        const response = await fetch('view_log.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-Token': document.querySelector('meta[name="csrf-token"]').content,
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify({ 
                id: logId,
                type: logType 
            })
        });

        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }

        const data = await response.json();
        
        // Create modal content based on log type
        const modalHTML = `
            <div class="modal fade" id="logDetailsModal" tabindex="-1">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">${logType === 'admin' ? 'Admin' : 'User'} Log Details #${logId}</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <dl class="row">
                                ${logType === 'admin' ? `
                                    <dt class="col-sm-4">Admin ID:</dt>
                                    <dd class="col-sm-8">${data.admin_id || 'Unknown'}</dd>
                                    
                                    <dt class="col-sm-4">Action:</dt>
                                    <dd class="col-sm-8">${data.action || 'Unknown'}</dd>
                                    
                                    <dt class="col-sm-4">Details:</dt>
                                    <dd class="col-sm-8">${data.details || 'No details available'}</dd>
                                ` : `
                                    <dt class="col-sm-4">User:</dt>
                                    <dd class="col-sm-8">${data.email || 'Unknown'}</dd>
                                    
                                    <dt class="col-sm-4">Status:</dt>
                                    <dd class="col-sm-8">
                                        <span class="badge bg-${data.status === 'success' ? 'success' : 'danger'}">
                                            ${data.status}
                                        </span>
                                    </dd>
                                    
                                    <dt class="col-sm-4">User Agent:</dt>
                                    <dd class="col-sm-8">${data.user_agent || 'Unknown'}</dd>
                                `}
                                
                                <dt class="col-sm-4">IP Address:</dt>
                                <dd class="col-sm-8">${data.ip_address}</dd>
                                
                                <dt class="col-sm-4">Timestamp:</dt>
                                <dd class="col-sm-8">${data.timestamp || data.created_at || 'Unknown'}</dd>
                            </dl>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        </div>
                    </div>
                </div>
            </div>`;

        // Remove existing modal if present
        const existingModal = document.getElementById('logDetailsModal');
        if (existingModal) {
            existingModal.remove();
        }

        // Add modal to document
        document.body.insertAdjacentHTML('beforeend', modalHTML);

        // Initialize and show modal
        const modal = new bootstrap.Modal(document.getElementById('logDetailsModal'));
        modal.show();

    } catch (error) {
        console.error('Error:', error);
        alert('Failed to load log details. Please try again.');
    }
}

/**
 * Block an IP address
 * @param {string} ip - The IP address to block
 */
async function blockIP(ip) {
    if (!ip) return;

    try {
        const response = await fetch('block_ip.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-Token': document.querySelector('meta[name="csrf-token"]').content,
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify({ ip_address: ip })
        });

        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }

        const result = await response.json();
        
        if (result.success) {
            alert('IP address has been blocked successfully.');
            window.location.reload();
        } else {
            throw new Error(result.message || 'Failed to block IP address');
        }

    } catch (error) {
        console.error('Error:', error);
        alert('Failed to block IP address: ' + error.message);
    }
}