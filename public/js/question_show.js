/**
 * Simple (ugly) code to handle the comment vote up/down
 */
const container = $('.js-vote-arrows');
container.find('a').on('click', function(e) {
    e.preventDefault();
    const $link = $(e.currentTarget);
    $.ajax({
        url: '/comments/10/vote/'+$link.data('direction'),
        method: 'POST'
    }).then(function(response) {
        container.find('.js-vote-total').text(response.votes);
    });
});