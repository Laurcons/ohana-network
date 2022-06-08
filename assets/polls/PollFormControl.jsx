import React from "react";
import { useState, useEffect } from "react";

const dataElem = document.getElementById("form_dataJson"); // from symfony

// answers type
const AT_SINGLE_SELECT = "single_select";
const AT_MULTI_SELECT = "multi_select";
// results display mode
const RES_ALWAYS = "always";
const RES_AFTER = "after";
const RES_NEVER = "never";

function PollFormControl(props) {

    let [ dataTree, setDataTree ] = useState({
        title: "",
        description: "",
        answersType: AT_SINGLE_SELECT,
        resultsMode: RES_ALWAYS,
        answers: []
    });
    function updateDataJson(updater) {
        let newd = updater(Object.assign({}, dataTree));
        setDataTree(newd);
        dataElem.value = JSON.stringify(newd);
    }

    function handleSubmit() {
      if (dataTree.title.trim().length === 0) {
        alert("The question cannot be empty.");
        return false;
      }
      if (dataTree.answers.length === 0) {
        alert("You need to add at least one answer!");
        return false;
      }
      if (dataTree.answers.some(a => a.text.trim().length === 0)) {
        alert("All answers need to have a value.");
        return false;
      }
      return true;
    }
    
    useEffect(() => {
      if (props.editMode)
        setDataTree(JSON.parse(dataElem.value));
      else
        updateDataJson(t => t);
    }, []);

    return (
      <>
        <div className="mb-3">
          <label className="form-label">Question</label>
          <input
            className="form-control"
            type="text"
            value={dataTree.title}
            onChange={ev =>
              updateDataJson(t => {
                t.title = ev.target.value;
                return t;
              })
            }
          />
          <small className="text-muted">
            Write your question here. What do you want to ask the group?
          </small>
        </div>
        <div className="mb-3">
          <label className="form-label">Description</label>
          <input
            className="form-control"
            type="text"
            value={dataTree.description}
            onChange={ev =>
              updateDataJson(t => {
                t.description = ev.target.value;
                return t;
              })
            }
          />
          <small className="text-muted">Describe the question in a bit more words, maybe.</small>
        </div>
        <h2>Answers</h2>
        <button
          type="button"
          className="btn btn-outline-primary mb-3"
          onClick={ev =>
            updateDataJson(t => {
              if (t.answers.length >= props.maxAnswers) {
                alert("Woah. Slow down there with the answers!");
                return t;
              }
              t.answers.push({ text: "" });
              return t;
            })
          }
        >
          Add answer
        </button>
        {dataTree.answers.map((answer, idx) => (
          <div className="input-group mb-3">
            <span className="input-group-text">{idx + 1}</span>
            <input
              className="form-control"
              type="text"
              value={answer.text}
              onChange={ev =>
                updateDataJson(t => {
                  t.answers[idx].text = ev.target.value;
                  return t;
                })
              }
            ></input>
            <button
              type="button"
              className="btn btn-outline-danger"
              onClick={ev =>
                updateDataJson(t => {
                  t.answers.splice(idx, 1);
                  return t;
                })
              }
            >
              &times;
            </button>
          </div>
        ))}
        <h2>Options</h2>
        <div className="mb-3">
          <label>Answers type</label>
          <div className="form-check">
            <input
              className="form-check-input"
              id="single-select-radio"
              type="radio"
              checked={dataTree.answersType == AT_SINGLE_SELECT}
              onChange={() =>
                updateDataJson(t => {
                  t.answersType = AT_SINGLE_SELECT;
                  return t;
                })
              }
            />
            <label className="form-check-label" htmlFor="single-select-radio">
              Only one answer per person (single select)
            </label>
          </div>
          <div className="form-check">
            <input
              className="form-check-input"
              type="radio"
              id="multi-select-radio"
              checked={dataTree.answersType == AT_MULTI_SELECT}
              onChange={() =>
                updateDataJson(t => {
                  t.answersType = AT_MULTI_SELECT;
                  return t;
                })
              }
            />
            <label className="form-check-label" htmlFor="multi-select-radio">
              Multiple answers per person (multi select)
            </label>
          </div>
        </div>
        <div className="mb-3">
          <label>Results mode</label>
          <div className="form-check">
            <input
              className="form-check-input"
              id="results-always-radio"
              type="radio"
              checked={dataTree.resultsMode == RES_ALWAYS}
              onChange={() =>
                updateDataJson(t => {
                  t.resultsMode = RES_ALWAYS;
                  return t;
                })
              }
            />
            <label className="form-check-label" htmlFor="results-always-radio">
              Always display
            </label>
          </div>
          <div className="form-check">
            <input
              className="form-check-input"
              type="radio"
              id="results-after-radio"
              checked={dataTree.resultsMode == RES_AFTER}
              onChange={() =>
                updateDataJson(t => {
                  t.resultsMode = RES_AFTER;
                  return t;
                })
              }
            />
            <label className="form-check-label" htmlFor="results-after-radio">
              Display only after person voted
            </label>
          </div>
          <div className="form-check">
            <input
              className="form-check-input"
              type="radio"
              id="results-never-radio"
              checked={dataTree.resultsMode == RES_NEVER}
              onChange={() =>
                updateDataJson(t => {
                  t.resultsMode = RES_NEVER;
                  return t;
                })
              }
            />
            <label className="form-check-label" htmlFor="results-never-radio">
              Never display
            </label>
          </div>
          <small className="text-muted">You will always be able to see the results.</small>
        </div>
        <div className="mb-3">
          <button
            type="submit"
            name="form[submit]"
            className="btn btn-primary"
            onClick={ev => handleSubmit() || ev.preventDefault()}
          >
            {props.editMode ? "Edit poll" : "Create poll"}
          </button>
        </div>
      </>
    );
}

export default PollFormControl;