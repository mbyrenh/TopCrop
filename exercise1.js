/**
 *  Acts as class representing a JSON-encoded
 *  HTTP POST request.
 */
function JSONRequest(URL, Data) {

    // member functions
    this.Send       = Send;

    // prepare request
    var m_request = new XMLHttpRequest();
    InitialiseRequest();

    /**
     * Initialise the basic POST request.
     */
    function InitialiseRequest () {
        m_request.open ("POST", URL, false);
        m_request.setRequestHeader ("Content-Type", "application/x-www-form-urlencoded");
    }

    /**
     * Sends the request and returns
     * its result.
     */
    function Send () {
        m_request.send(Data);
        return JSON.parse(m_request.responseText);
        //return m_request.responseText;
    }

}

/**
 *  Create a request for a new pair of colors.
 */
function ColourRequest (URL) {
    var request = new JSONRequest (URL, { "task" : "getColours" });
    return request;
}

/**
 * Requests a new pair of colors from the
 * PHP API.
 */
function updateColours (element_id) {

    try {
        var Request = new ColourRequest("exercise1.php");
        var Response = Request.Send ();

        document.getElementById("testbutton").style.background = Response.background;
        document.getElementById("testbutton").style.border = "5px solid " + Response.borders;

    } catch (error) {

        console.log ("Error: " + error)

    }
}
