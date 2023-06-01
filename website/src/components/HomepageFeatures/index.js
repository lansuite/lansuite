import React from 'react';
import clsx from 'clsx';
import styles from './styles.module.css';

const FeatureList = [
  {
    title: 'Organisation of LAN-Parties',
    Svg: require('@site/static/img/undraw_docusaurus_mountain.svg').default,
    description: (
      <>
        Announce a new LAN-Party and enable people to sign up with a direct payment flow 
        and several follow-up actions like Clan creation and more.
      </>
    ),
  },
  {
    title: 'Organisation of tournaments',
    Svg: require('@site/static/img/undraw_docusaurus_tree.svg').default,
    description: (
      <>
        Manage tournaments for multiple games with different modes like single- and double-elimination, 
        league, or group games with a KO strategy.
      </>
    ),
  },
  {
    title: 'Seat plans',
    Svg: require('@site/static/img/undraw_docusaurus_react.svg').default,
    description: (
      <>
        Define a seating plan with several rooms and areas and let LAN Party attendees choose their seats in advance. 
        This enables clans to sit together to facilitate better team play.
      </>
    ),
  },
  /*
  {
    title: 'Projector support',
    Svg: require('@site/static/img/undraw_docusaurus_react.svg').default,
    description: (
      <>
        Show the latest content like news messages, the current state of a tournament, 
        or a timetable on a wall via the projector mode during the party to inform attendees.
      </>
    ),
  },
  */
];

function Feature({Svg, title, description}) {
  return (
    <div className={clsx('col col--4')}>
      <div className="text--center">
        <Svg className={styles.featureSvg} role="img" />
      </div>
      <div className="text--center padding-horiz--md">
        <h3>{title}</h3>
        <p>{description}</p>
      </div>
    </div>
  );
}

export default function HomepageFeatures() {
  return (
    <section className={styles.features}>
      <div className="container">
        <div className="row">
          {FeatureList.map((props, idx) => (
            <Feature key={idx} {...props} />
          ))}
        </div>
      </div>
    </section>
  );
}
