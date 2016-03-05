
/**
 * Updates any element on the page that has the class timestamp and has a data-timestamp attribute
 */
function updateTimestamps() {
    var currentTimestamp = Math.round(new Date().getTime() / 1000);
    $('.timestamp').each(function(idx, el){
        var postTimestamp = $(el).data('timestamp');
        if (!postTimestamp) {
            // If we don't have a timestamp data field, there's nothing we can do
            // return early
            return;
        }

        var elapsed = currentTimestamp - postTimestamp;
        var postDate = new Date();
        postDate.setTime(postTimestamp * 1000);

        if (elapsed < 60) { // up to: 59 seconds ago
            $(el).text(elapsed + ' seconds ago');
        } else if (elapsed < 3600) { // up to: 59 minutes ago
            var minutes = Math.floor(elapsed/60);
            if (1 === minutes) {
                $(el).text('1 minute ago');
            } else {
                $(el).text(minutes + ' minutes ago');
            }
        } else if (elapsed < 86400) { // up to: 24 hours ago
            var hours = Math.floor(elapsed/3600);
            if (1 === hours) {
                $(el).text(1 + ' hour ago');
            } else {
                $(el).text(hours + ' hours ago');
            }
        } else if (elapsed < 604800) { // up to: 7 days ago
            var days = Math.floor(elapsed/86400);
            if (1 === days) {
                $(el).text('yesterday at ' + formatTime(postDate));
            } else {
                $(el).text(days + ' days ago');
            }
        } else {
            $(el).removeClass('timestamp').text(formatDate(postDate) + ' ' + formatTime(postDate));
        }
    });
}

/**
 * Formats a human readable date from the provided Date object
 * @param {Date} dateObject
 * @returns {string} January 12, 2016
 */
function formatDate(dateObject) {
    var months = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];
    return months[dateObject.getMonth()] + ' ' + dateObject.getDate() + ', ' + dateObject.getFullYear();
}

/**
 * Formats a human readable time from the provided Date object
 * @param {Date} dateObject
 * @returns {string} 12:05am
 */
function formatTime(dateObject) {
    var hours = dateObject.getHours();
    var minutes = dateObject.getMinutes();
    var ampm = 'am';
    if (hours >= 12) {
        hours = hours - 12;
        ampm = 'pm';
    }
    if (0 === hours) {
        hours = 12;
    }
    if (minutes < 10) {
        minutes = '0' + minutes;
    }
    return hours + ':' + minutes + ampm;
}


// Start the timer
$(document).ready(function(){
    document.timestampUpdates = setInterval(updateTimestamps, 1000);
});

// Do any page cleanup, remove timers etc
$(document).unload(function(){
    clearInterval(document.timestampUpdates);
});
