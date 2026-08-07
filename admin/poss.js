function openReportModal(postId) {
    document.getElementById('report_post_id').value = postId;
    document.getElementById('report_reason').value = '';
    document.getElementById('reportModal').style.display = 'flex';
    document.getElementById('report_reason').focus();
}

function closeReportModal() {
    document.getElementById('reportModal').style.display = 'none';
}
window.onclick = function(event) {
    var modal = document.getElementById('reportModal');
    if (event.target == modal) {
        closeReportModal();
    }
}

function togglecomments(divId) {
    var replyDiv = document.getElementById(divId);
    if (replyDiv.style.display === 'none' || replyDiv.style.display === '') {
        replyDiv.style.display = 'block';
    } else {
        replyDiv.style.display = 'none';
    }
}

function toggleReplyForm(commentId) {
    var replyForm = document.getElementById('reply-' + commentId);
    if (replyForm.style.display === 'none' || replyForm.style.display === '') {
        replyForm.style.display = 'block';
    } else {
        replyForm.style.display = 'none';
    }
}

function directimage(image) {
    var image = Image;
    // Redirect to the image path (example: '/images/')
    window.location.href = '/uploads/' + $image;
}