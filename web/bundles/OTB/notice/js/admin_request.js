/* eslint-disable quotes */

$(document).ready(function () {
  
  getDashStatistics_2();
  getDashStatistics_3();
  getDashStatistics_4();
  getDashStatistics_5();
  getNumberOfComments();
  getRegulationStatistics();
  getRegulationsPerAgency();
  getClosedRegulations();
  manageAbusiveComments();

  
  var dashStatistics_2 = setInterval(getDashStatistics_2, 500015);
  var dashStatistics_3 = setInterval(getDashStatistics_3, 500020);
  var dashStatistics_4 = setInterval(getDashStatistics_4, 500025);
  var dashStatistics_5 = setInterval(getDashStatistics_5, 500030);
  var getRegulationStats = setInterval(getRegulationStatistics, 500035);
  var getRegulationStats = setInterval(getNumberOfComments, 500040);
  var getRegulations = setInterval(getRegulationsPerAgency, 500045);
  var closeRegulations = setInterval(getClosedRegulations, 50000);
  var manageAbusiveComments = setInterval(manageAbusiveComments, 50000);

  var closeRegulation = function closeRegulations() {
    var statistics = $.ajax({ url: '/index.php/consultation/regulations/close', type: 'get', data: {}, datatype: 'JSON' });
    statistics.done(function (response) {
      response = JSON.parse(response);
      console.log(response);
    });
  }

  function manageAbusiveComments() {
  var closingRegulations = $.ajax({ url: '/login-admin/manageabusiveterms_create/check_activity/', type: 'get', data: {}, datatype: 'JSON' });
  closingRegulations.done(function (response) {
    response = JSON.parse(response);
  });
} 
});

function getClosedRegulations() {
  var statistics = $.ajax({ url: '/index.php/consultation/regulations/close', type: 'get', data: {}, datatype: 'JSON' });
  statistics.done(function (response) {
    response = JSON.parse(response);
    console.log(response);
  });
}

function getDashStatistics_2() {
  let statistics = $.ajax({ url: '/index.php/dash/stats_2', type: 'get', data: {}, datatype: 'JSON' });
  statistics.done(function (response) {
    response = JSON.parse(response);
    $("#business_types").html(response.business_types);
    $("#activities").html(response.activities);
  });
}
function getDashStatistics_3() {
  let statistics = $.ajax({ url: '/index.php/dash/stats_3', type: 'get', data: {}, datatype: 'JSON' });
  statistics.done(function (response) {
    response = JSON.parse(response);
    $("#issuing_authority").html(response.agencies);
    $("#news_item").html(response.news);
  });
}
function getDashStatistics_4() {
  let statistics = $.ajax({ url: '/index.php/dash/stats_4', type: 'get', data: {}, datatype: 'JSON' });
  statistics.done(function (response) {
    response = JSON.parse(response);
    $("#newsletters").html(response.newsletters);
    $("#newsletters_subscribers").html(response.subscribers);
  });
}
function getDashStatistics_5() {
  let statistics = $.ajax({ url: '/index.php/dash/stats_5', type: 'get', data: {}, datatype: 'JSON' });
  statistics.done(function (response) {
    response = JSON.parse(response);
    $("#regulation_users").html(response.users);
    //$("#admin").html(response.admin);
    $("#regulation_groups").html(response.groups);
  });
}

function getNumberOfComments() {
  let statistics = $.ajax({ url: '/index.php/dash/no_comments', type: 'get', data: {}, datatype: 'JSON' });
  statistics.done(function (response) {
    response = JSON.parse(response);
    $("#number_of_comments").html(response.number_of_comments);
  });
}

function getNumberOfComments() {
  let statistics = $.ajax({ url: '/index.php/dash/comments_stats', type: 'get', data: {}, datatype: 'JSON' });
  statistics.done(function (response) {
    response = JSON.parse(response);
    $("#regulation_numerous_comments").html(
      response.high_comments[0]
    );
    $("#comment_number_regulation").html(
      response.high_comments[1]
    );
    $("#regulation_with_comment").html(
      response.high_expression[0]
    );
    $("#regulation_comment_count").html(
      response.high_expression[1]
    );
    $("#abusive_comments").html(response.abusive_comments);
  });
}

function getRegulationStatistics() {
  let statistics = $.ajax({ url: '/index.php/dash/regulation_stats', type: 'get', data: {}, datatype: 'JSON' });
  statistics.done(function (response) {
    response = JSON.parse(response);
    $('#all_regulations').html(response.all);
    $('#published_regulations').html(response.published);
    $('#closed_regulations').html(response.closed);
    $('#unpublished_regulations').html(response.unpublished);
    $('#open_regulations').html(response.open);
    $('#seven_regulations').html(response.seven_all);
    $("#internal_consultations").html(response.internal_consultations);
    $("#public_consultations").html(response.public_consultations);
  });
}

function getRegulationsPerAgency() {
  let regulations = $.ajax({ url: '/index.php/dash/regulation_agency', type: 'get', data: {}, datatype: 'JSON' });
  regulations.done(function (response) {
    response = JSON.parse(response);
    $.each(response, function (i, data) {
      $("#regulations_per_agency").append(
        "<tr><td>" + i + "</td>" + "<td>" + data + "</td></tr>"
      );
    });
  });
}
