/* eslint-disable quotes */
$(document).ready(function () {
    getDashStatistics_1();
    getDashStatistics_2();
    getDashStatistics_3();
    getDashStatistics_4();
    getDashStatistics_5();
    getDashStatistics_6();
    getDashStatistics_7();
    getDashStatistics_8();
    getDashStatistics_9();
    getDashStatistics_10();
    getDashStatistics_11();
    getClosedRegulations();
    getDashStatistics_webstats_1();
});
var dashStatistics_1 = setInterval(getDashStatistics_webstats_1, 500010);
var dashStatistics_1 = setInterval(getDashStatistics_1, 150001);
var dashStatistics_2 = setInterval(getDashStatistics_2, 150005);
var dashStatistics_3 = setInterval(getDashStatistics_3, 150002);
var dashStatistics_4 = setInterval(getDashStatistics_4, 150005);
var dashStatistics_5 = setInterval(getDashStatistics_5, 150003);
var dashStatistics_6 = setInterval(getDashStatistics_6, 150000);
var dashStatistics_7 = setInterval(getDashStatistics_7, 150004);
var dashStatistics_8 = setInterval(getDashStatistics_8, 150005);
var dashStatistics_9 = setInterval(getDashStatistics_9, 150000);
var dashStatistics_10 = setInterval(getDashStatistics_10, 150010);
var dashStatistics_11 = setInterval(getDashStatistics_11, 150010);
var closeRegulations = setInterval(getClosedRegulations, 150000);

function getDashStatistics_1() {
    let statistics = $.ajax({ url: '/login-admin/admin/dashboard-starts_1', type: 'get', data: {}, datatype: 'JSON' });
    statistics.done(function (response) {
        response = JSON.parse(response);
        $("#industries").html(response.webStats.industries);
        $("#createdindustries").ahtml(response.webStats.createdindustries);
        $("#removedindustries").html(response.webStats.removedindustries);
    });
}
function getDashStatistics_2() {
    let statistics = $.ajax({ url: '/login-admin/admin/dashboard-starts_2', type: 'get', data: {}, datatype: 'JSON' });
    statistics.done(function (response) {
        response = JSON.parse(response);
        $("#locations").html(response.webStats.locations);
        $("#removedlocations").html(response.webStats.removedlocations);
        $("#createdlocations").html(response.webStats.createdlocations);
    });
}
function getDashStatistics_3() {
    let statistics = $.ajax({ url: '/login-admin/admin/dashboard-starts_3', type: 'get', data: {}, datatype: 'JSON' });
    statistics.done(function (response) {
        response = JSON.parse(response);
        $("#agencies").html(response.webStats.agencies);
        $("#removedagencies").html(response.webStats.removedagencies);
        $("#createdagencies").html(response.webStats.createdagencies);
    });
}
function getDashStatistics_4() {
    let statistics = $.ajax({ url: '/login-admin/admin/dashboard-starts_4', type: 'get', data: {}, datatype: 'JSON' });
    statistics.done(function (response) {
        response = JSON.parse(response);
        $("#users").html(response.webStats.users);
        $("#removedusers").html(response.webStats.removedusers);
        $("#createdusers").html(response.webStats.createdusers);
    });
}
function getDashStatistics_5() {
    let statistics = $.ajax({ url: '/login-admin/admin/dashboard-starts_5', type: 'get', data: {}, datatype: 'JSON' });
    statistics.done(function (response) {
        response = JSON.parse(response);
        $("#groups").html(response.webStats.groups);
        $("#removedgroups").html(response.webStats.removedgroups);
        $("#createdgroups").html(response.webStats.createdgroups);
    });
}
function getDashStatistics_6() {
    let statistics = $.ajax({ url: '/login-admin/admin/dashboard-starts_6', type: 'get', data: {}, datatype: 'JSON' });
    statistics.done(function (response) {
        response = JSON.parse(response);
        $("#subscribers").html(response.webStats.subscribers);
        $("#removedsubscribers").html(response.webStats.removedsubscribers);
        $("#createdsubscribers").html(response.webStats.createdsubscribers);
    });
}
function getDashStatistics_7() {
    let statistics = $.ajax({ url: '/login-admin/admin/dashboard-starts_7', type: 'get', data: {}, datatype: 'JSON' });
    statistics.done(function (response) {
        response = JSON.parse(response);
        $("#newsletters").html(response.webStats.newsletters);
        $("#removednewsletters").html(response.webStats.removednewsletters);
        $("#creatednewsletters").html(response.webStats.creatednewsletters);
        $("#news").html(response.webStats.news);
        $("#removednews").html(response.webStats.removednews);
        $("#creatednews").html(response.webStats.creatednews);
    });
}
function getDashStatistics_8() {
    let statistics = $.ajax({ url: '/login-admin/admin/dashboard-starts_8', type: 'get', data: {}, datatype: 'JSON' });
    statistics.done(function (response) {
        response = JSON.parse(response);
        $("#publishedlicenses").html(response.webStats.published_licenses);
        $("#unpublishedlicenses").html(response.webStats.unpublished_licenses);
        $("#createdlicenses").html(response.webStats.createdlicenses);
        $("#removedlicenses").html(response.webStats.removedlicenses);
        $("#in_review").html(response.webStats.in_review);
        $("#draft_applications").html(response.webStats.draft);
    });
}
function getDashStatistics_9() {
    let statistics = $.ajax({ url: '/login-admin/admin/dashboard-starts_9', type: 'get', data: {}, datatype: 'JSON' });
    statistics.done(function (response) {
        response = JSON.parse(response);
        $("#feedback").html(response.webStats.feedback);
        $("#removedfeedback").html(response.webStats.removedfeedback);
        $("#createdfeedback").html(response.webStats.createdfeedback);
    });
}
function getDashStatistics_10() {
    let statistics = $.ajax({ url: '/login-admin/admin/dashboard-starts_10', type: 'get', data: {}, datatype: 'JSON' });
    statistics.done(function (response) {
        response = JSON.parse(response);
        $("#activities").html(response.webStats.activities);
        $("#removedactivities").html(response.webStats.removedactivities);
        $("#createdactivities").html(response.webStats.createdactivities);
    });
}
function getDashStatistics_11() {
    let statistics = $.ajax({ url: '/login-admin/admin/dashboard-starts_11', type: 'get', data: {}, datatype: 'JSON' });
    statistics.done(function (response) {
        response = JSON.parse(response);
        $("#businesstypes").html(response.webStats.businesstypes);
        $("#removedbusinesstypes").html(response.webStats.removedbusinesstypes);
        $("#createdbusinesstypes").html(response.webStats.createdbusinesstypes);
    });
}
function getClosedRegulations() {
    var statistics = $.ajax({ url: '/index.php/consultation/regulations/close', type: 'get', data: {}, datatype: 'JSON' });
    statistics.done(function (response) {
        response = JSON.parse(response);
        console.log(response);
    });
}