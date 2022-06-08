import "core-js";
import React from "react";
import { createRoot } from "react-dom/client";
import PollFormControl from "./PollFormControl";

function main() {
    let domelem = document.querySelector(".js-poll-control");
    if (domelem === null)
        return;
    //let token = domelem.getAttribute("data-token");
    let root = createRoot(domelem);
    root.render(<PollFormControl maxAnswers={50} />);
}

main();