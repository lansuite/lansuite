const React = require('react');

const CompLibrary = require('../../core/CompLibrary.js');
const Container = CompLibrary.Container;

class Credits extends React.Component {
  render() {
    let language = this.props.language || '';

    return (
      <div className="docMainWrapper wrapper">
        <Container className="mainContainer documentContainer postContainer">
          <div className="post">
            <header className="postHeader">
              <h2>Credits</h2>
            </header>
            <h3>Graphics</h3>
            <ul>
              <li><a href="https://thenounproject.com/term/tournament/1574981/">Tournament Icon</a>: tournament by 1516 from the Noun Project</li>
              <li><a href="https://thenounproject.com/term/seats/1722122/">Seats Icon</a>: seats by Made x Made from the Noun Project</li>
              <li><a href="https://thenounproject.com/search/?q=computers&i=1750648">Computer Icon</a>: Computer by angelina fara from the Noun Project</li>
            </ul>
          </div>
        </Container>
      </div>
    );
  }
}

module.exports = Credits;
