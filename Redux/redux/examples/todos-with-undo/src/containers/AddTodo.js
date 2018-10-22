import React from 'react'
import { connect } from 'react-redux'
import * as TodoActions  from '../actions'

class AddTodo extends React.Component {
  constructor(props) {
    super(props);
    this.state = { id: -1, text:'' };
    this.handle_addTodo=this.handle_addTodo.bind(this);
    this.handle_saveTodo=this.handle_saveTodo.bind(this);
  }

  handle_saveTodo(text)
  {
    this.setState({...this.state, text:text});
    this.props.saveTodo(this.state.id, text);
  }

  handle_addTodo(text)
  {
    this.props.addTodo(text)
  }

  render()
  {
    let input;
    let issaving = 0;

    return (
      <div>
        <form onSubmit={e => {
          e.preventDefault()
          if (!input.value.trim()) {
            return
          }
          issaving = this.state.id >= 0;//Number.parseInt(inputid.value, 10);//!inputid.value.trim();
          
          if (issaving)
          {
            input.value = this.state.text;
            this.handle_saveTodo(input.value);
          }
          else
          {
            this.handle_addTodo(input.value)
          }

            
        }}>
        <input ref={node => {
          input = node
        }} />
        { !issaving? 
          <button type="submit">
            Add Todo
          </button>
          :
          <button type="submit">
            Save Todo}
          </button>
        }
          

        </form>
      </div>
    )
  }
}
const mapStatetoProps = (state) =>
({
  id: state.id,
  text: state.text
})
const mapDispatchtoProps = (dispatch) =>
({
  addTodo:(text) => dispatch(TodoActions.addTodo(text)),
  saveTodo:(id, text) => dispatch(TodoActions.saveTodo(id, text))
})
AddTodo = connect(mapStatetoProps, mapDispatchtoProps)(AddTodo)


export default AddTodo

