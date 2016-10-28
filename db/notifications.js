(function (doc) {
    if (doc.type === "event") {
        var start = null, end = null;
        if (doc.startDate) {
            start = doc.startDate + " " + (doc.startTime || "12:00 AM");
        }

        if (doc.endDate) {
            end = doc.endDate + " " + (doc.endTime || "12:00 AM");
        }

        if (start && end && doc.profile) {
            emit([
                start,
                end
            ], {
                "type": "event",
                "profile": doc.profile
            });
        }
    }
})