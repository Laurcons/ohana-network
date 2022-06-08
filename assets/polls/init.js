import "core-js";
import React from "react";
import { createRoot } from "react-dom/client";
import PollFormControl from "./PollFormControl";

function main() {
    let domelem = document.querySelector(".js-poll-control");
    if (domelem === null)
        return;
    let editMode = domelem.getAttribute("data-edit-mode") == "true";
    let root = createRoot(domelem);
    root.render(<PollFormControl maxAnswers={50} editMode={editMode} />);
}

main();