
$(document).ready(function () {
  getClosedRegulations();
  getClosingRegulations();
  getTrendsRegulation();
  getAgencyList();
  getIndustryList();
  getKeywordList();
  getSearchTerms();
  getStatistics();
  closeRegulationsAction();

  var statisticsList = setInterval(getStatistics, 50000);
  var closeRegulations = setInterval(closeRegulationsAction, 50000);
});

// this two functions could be combined
// to be re-factored

function likeReplyFunction(comment) {
  var likeReply = $.ajax({ url: "/index.php/comments/" + comment + "/like", type: "PUT", data: {}, datatype: 'JSON' });
  likeReply.done(function (response) {
    response = JSON.parse(response);
    $("#reply_like_count_" + comment).html(response.like_count);
  });
}

function likeCommentFunction(comment) {
  var likeComment = $.ajax({ url: "/index.php/comments/" + comment + "/like", type: "PUT", data: {}, datatype: 'JSON' });
  likeComment.done(function (response) {
    response = JSON.parse(response);
    $("#like_count_" + comment).html(response.like_count);
  });
}

function getAgencyList() {
  var agency = $.ajax({ url: '/index.php/consultation/regulations/agency/list', type: 'get', data: {}, datatype: 'JSON' });
  agency.done(function (response) {
    response = JSON.parse(response);

    let select = $("#selectAgencies");
    $.each(response, function (key, value) {
      select.append('<option id="' + key + '">' + value + '</option>')
    });
  });
}

function getIndustryList() {
  var industry = $.ajax({ url: '/index.php/consultation/regulations/industry/list', type: 'get', data: {}, datatype: 'JSON' });
  industry.done(function (response) {
    response = JSON.parse(response);

    let select = $("#selectIndustries");
    $.each(response, function (key, value) {
      select.append('<option id="' + key + '">' + value + '</option>')
    });
  });
}

function getStatistics() {
  var statistics = $.ajax({ url: '/index.php/consultation/regulations/count', type: 'get', data: {}, datatype: 'JSON' });
  statistics.done(function (response) {
    response = JSON.parse(response);
    $('.count_all').html(response.all);
    $('.open_all').html(response.open);
    $('.closed_all').html(response.closed);
    $('.seven_all').html(response.seven_all);
    $('.trending_all').html(response.trending_all);
    $('.published_all').html(response.published);
  });
}

// function closeRegulations() {
//   var statistics = $.ajax({ url: '/index.php/consultation/regulations/close', type: 'get', data: {}, datatype: 'JSON' });
//   statistics.done(function (response) {
//     response = JSON.parse(response);
//   });
// }


function getClosedRegulations() {
  var regulation = $.ajax({ url: '/index.php/consultation/regulations/regulations_completed', type: 'get', data: {}, datatype: 'JSON' });
  regulation.done(function (response) {
    response = JSON.parse(response);
    var closing = $("#closed_consolations");
    $.each(response, function (key, value) {
      closing.append(
        "<li class='list-group-item d-flex justify-content-between align-items-center'><a href='/index.php/consultation/regulations/details/" + key + "'>" + value + "</a></li>"
      );
    });
  });
}

function getClosingRegulations() {
  var closingRegulations = $.ajax({ url: '/index.php/consultation/regulations/regulations_closing', type: 'get', data: {}, datatype: 'JSON' });
  closingRegulations.done(function (response) {
    if (response) {
      response = JSON.parse(response);
      var closing = $("#closing_consolations");
      $.each(response, function (key, value) {
        closing.append(
          "<li class='list-group-item d-flex justify-content-between align-items-center'><a href='/index.php/consultation/regulations/details/" + key + "'>" + value + "</a></li>"
        )
      });
    }
  });
}

function closeRegulationsAction() {
  var closingRegulations = $.ajax({ url: '/index.php/consultation/regulations/close', type: 'get', data: {}, datatype: 'JSON' });
  closingRegulations.done(function (response) {
    response = JSON.parse(response);
  });
}

function getTrendsRegulation() {
  var trendsRegulation = $.ajax({ url: '/index.php/consultation/regulations/trends', type: 'get', data: {}, datatype: 'JSON' });
  trendsRegulation.done(function (response) {
    response = JSON.parse(response);
    var key_list = $("#trending_consolations");
    $.each(response, function (key, value) {
      key_list.append(
        "<li class='list-group-item d-flex justify-content-between align-items-center'><a href='/index.php/consultation/regulations/details/" + key + "'>" + value + "</a></li>"
      )
    })
  });
}

function getKeywordList() {
  var keywordList = $.ajax({ url: '/index.php/consultation/regulations/keywords/list', type: 'get', data: {}, datatype: 'JSON' });
  keywordList.done(function (response) {
    response = JSON.parse(response);
    var key_list = [];
    $.each(response, function (key, value) {
      key_list.push(value);
    })
    $("#selectKeywords").autocomplete({
      source: key_list
    })
  });
}

function getSearchTerms() {
  var searchTerms = $.ajax({ url: '/index.php/consultation/regulations/search/terms', type: 'get', data: {}, datatype: 'JSON' });
  $("#quick_search_terms").keyup(function () {
    searchTerms.done(function (response) {
      response = JSON.parse(response);
      $("#quick_search_terms").autocomplete({
        source: response
      });
    });
  });
}
